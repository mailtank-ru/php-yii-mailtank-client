<?php
/**
 * Class MailtankRecord
 * @property-read string $endpoint
 * @property-read string $isNewRecord
 * @var MailtankClient Yii::app()->mailtank
 */
abstract class MailtankRecord extends \CModel
{
    public $id;

    protected $url;
    protected $isNewRecord;

    private static $_models = array();

    /**
     * Tells us if this api endpoint supports all of crud methods or only insert
     * @var bool
     */
    protected $createOnly = false;

    /**
     * @return boolean
     */
    public function getCreateOnly()
    {
        return $this->createOnly;
    }


    function __construct()
    {
        $this->setIsNewRecord(true);
    }


    public static function model($className = __CLASS__)
    {
        if (isset(self::$_models[$className]))
            return self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
            return $model;
        }
    }

    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array(
            'id',
        );
    }

    /**
     * @param string $pk (external_id || id)
     * @return bool|MailtankRecord
     * @throws MailtankException
     */
    public static function findByPk($pk)
    {
        $className = get_called_class();
        $model = new $className;

        if ($model->createOnly) {
            throw new MailtankException('This mailtank model supports only insert method.');
        }

        try {
            $data = Yii::app()->mailtank->sendRequest(
                $model::ENDPOINT . $pk,
                null,
                'get'
            );
        } catch (MailtankException $e) {
            if ($e->getCode() == 404) {
                return false;
            }
            throw $e;
        }

        if ($data) {
            if (!empty($data['message'])) {
                return false;
            }
            $model->setAttributes($data, false);
            $model->setIsNewRecord(false);
            return $model;
        }

        return false;
    }

    public function save($runValidation = true, $attributes = null)
    {
        if (!$runValidation || $this->validate($attributes))
            return $this->getIsNewRecord() ? $this->insert($attributes) : $this->update($attributes);
        else
            return false;
    }

    public function insert($attributes = null)
    {
        $this->scenario = 'insert';
        if (!$this->getIsNewRecord())
            throw new MailtankException('The mailtank record cannot be inserted to api because it is not new.');
        if ($this->beforeSave()) {
            $fields = $this->getAttributes($attributes);
            $fields = $this->beforeSendAttributes($fields);
            try {
                $data = Yii::app()->mailtank->sendRequest(
                    $this::ENDPOINT,
                    json_encode($fields),
                    'post'
                );
            } catch (MailtankException $e) {
                if ($e->validationErrors) {
                    $this->addErrors($e->validationErrors);
                    return false;
                } else {
                    throw $e;
                }
            }
            if (empty($data['id'])) {
                throw new MailtankException('Endpoint ' . $this::ENDPOINT . ' returned no id on insert');
            }

            $this->setAttributes($data, false);
            $this->setIsNewRecord(false);
            $this->afterSave();
            return true;
        }
        return false;
    }

    /**
     * @param null $attributes
     * @throws MailtankException
     * @return bool
     */
    public function update($attributes = null)
    {
        $this->scenario = 'update';
        if ($this->getIsNewRecord())
            throw new MailtankException(\Yii::t('yii', 'The active record cannot be updated because it is new.'));
        if ($this->beforeSave()) {
            $fields = $this->getAttributes($attributes);
            $fields = $this->beforeSendAttributes($fields);
            try {
                $data = Yii::app()->mailtank->sendRequest(
                    $this->url,
                    json_encode($fields),
                    'put'
                );
            } catch (MailtankException $e) {
                if ($e->validationErrors) {
                    $this->addErrors($e->validationErrors);
                    return false;
                } else {
                    throw $e;
                }
            }

            if (!empty($data['message'])) {
                return false;
            }

            $this->setAttributes($data, false);
            $this->afterSave();
            return true;
        } else
            return false;
    }

    public function refresh()
    {
        if ($this->createOnly) {
            throw new MailtankException('This mailtank model supports only refresh method.');
        }

        if (empty($this->url) || $this->getIsNewRecord()) {
            throw new MailtankException('This model is new and cant be refreshed.');
        }

        try {
            $data = Yii::app()->mailtank->sendRequest(
                $this->url,
                null,
                'get'
            );
        } catch (MailtankException $e) {
            if ($e->getCode() == 404) {
                return false;
            }
            throw $e;
        }

        if ($data) {
            if (!empty($data['message'])) {
                return false;
            }
            $this->setAttributes($data, false);
            return true;
        }

        return false;
    }


    /**
     * @return bool
     * @throws MailtankException
     */
    public function beforeSave()
    {
        if ($this->createOnly && !$this->getIsNewRecord()) {
            throw new MailtankException('This mailtank model supports only insert method.');
        }
        return true;
    }

    /**
     *
     */
    public function afterSave()
    {

    }

    public function beforeSendAttributes($fields)
    {
        $_tempFields = $fields;
        $fields = array();
        if ($_tempFields) {
            foreach ($_tempFields as $key => $value) {
                if ($value!==null) {
                    $fields[$key] = $value;
                }
            }
        }

        return $fields;
    }

    /**
     * @param bool $isNewRecord
     */
    protected function setIsNewRecord($isNewRecord)
    {
        $this->isNewRecord = $isNewRecord;
    }

    /**
     * @return bool
     */
    public function getIsNewRecord()
    {
        return $this->isNewRecord;
    }

    /**
     * @throws MailtankException
     * @return bool
     */
    public function delete()
    {
        if ($this->createOnly || $this->getIsNewRecord()) {
            throw new MailtankException('This mailtank model doesnt support delete method.');
        }

        Yii::app()->mailtank->sendRequest(
            $this->url,
            null,
            'delete'
        );
        return true;
    }
}