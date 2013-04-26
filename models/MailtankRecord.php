<?php
/**
 * Class MailtankRecord
 * @property-read string $endpoint
 * @property-read string $isNewRecord
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

    abstract public static function getEndpoint();


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
        $model = new get_class(self);

        if ($model->createOnly) {
            throw new MailtankException('This mailtank model supports only insert method.');
        }

        $data = Yii::app()->mailtank->sendRequest(
            self::getEndpoint() . $pk,
            null,
            'get'
        );

        if ($data) {
            $model->setAttributes($data, false);
            return $model;
        }

        return false;
    }

    /**
     * @param int $page
     * @return MailtankRecord[]
     * @throws MailtankException
     */
    public static function findAll($page) {
        $model = new get_class(self);

        if ($model->createOnly) {
            throw new MailtankException('This mailtank model supports only insert method.');
        }

        $data = Yii::app()->mailtank->sendRequest(
            self::getEndpoint() . ($page ? "?page=$page" : ''),
            null,
            'get'
        );

        $models = array();
        if($data['objects']) {
            foreach($data['objects'] as $attributes) {
                $_model = clone $model;
                $_model->setAttributes($attributes, false);
                $models[] = $_model;
            }
        }

        return $models;
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
            $data = Yii::app()->mailtank->sendRequest(
                self::getEndpoint(),
                json_encode($fields),
                'post'
            );
            if (empty($data['id'])) {
                throw new MailtankException('Endpoint ' . $this->endpoint . ' returned no id on insert');
            }

            $this->setAttributes($data, false);
            $this->setIsNewRecord(false);
            $this->afterSave();
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
            $data = Yii::app()->mailtank->sendRequest(
                $this->url,
                json_encode($fields),
                'post'
            );

            $this->setAttributes($data, false);
            $this->afterSave();
            return true;
        } else
            return false;
    }


    /**
     * @return bool
     * @throws MailtankException
     */
    public function beforeSave()
    {
        if (!$this->createOnly && !$this->getIsNewRecord()) {
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

    public function beforeSendAttributes($fields) {
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


}