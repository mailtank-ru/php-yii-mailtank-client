<?php
/**
 * Class MailtankMailing
 */
class MailtankMailing extends MailtankRecord {
    public $markup;
    public $name;
    protected $target;

    protected $createOnly = false;

    public static function model($className=__CLASS__)
	{
	     return parent::model($className);
	}

    public function rules()
    {
        return array(
            array('title', 'length', 'max' => 60),
            array('id', 'safe'),
            array('layout_id', 'safe'),
            array('tags, subscribers', 'safe'),
        );
    }


    public static function getEndpoint()
    {
        return '/mailings/';
    }

    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array_merge_recursive(parent::attributeNames(),array(
            'markup',
            'name',
            'tags',
            'subscribers'
        ));
    }

    public function beforeSave()
    {
        if($this->scenario == 'update') {
            throw new MailtankException('Update method is unsupported');
        }
        return parent::beforeSave();
    }


}