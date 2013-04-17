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

    abstract public function getEndpoint();


    public static function model($className=__CLASS__)
    {
        if(isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {
            $model=self::$_models[$className]=new $className(null);
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

    public static function findByPk($pk) {
        $model = clone self::model();

        if ($model->createOnly) {
            throw new MailtankException('This mailtank model supports only insert method.');
        }

        $data = Yii::app()->mailtank->sendRequest(
            $model->endpoint . $pk,
            null,
            'get'
        );

        var_dump($data);
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
        if (!$this->getIsNewRecord())
            throw new MailtankException('The mailtank record cannot be inserted to api because it is not new.');
        if ($this->beforeSave()) {
            $data = Yii::app()->mailtank->sendRequest(
                $this->endpoint,
                json_encode($this->getAttributes($attributes)),
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
    public function update($attributes=null) {
        if($this->getIsNewRecord())
            throw new MailtankException(\Yii::t('yii','The active record cannot be updated because it is new.'));
        if($this->beforeSave())
        {
            $data = Yii::app()->mailtank->sendRequest(
                $this->url,
                json_encode($this->getAttributes($attributes)),
                'post'
            );

            $this->setAttributes($data, false);
            $this->afterSave();
            return true;
        }
        else
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
    public function afterSave() {

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