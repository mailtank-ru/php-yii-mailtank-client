<?php
/**
 * Class MailtankLayout
 */
class MailtankLayout extends MailtankRecord {
    public $markup;
    public $name;

    protected $createOnly = false;

    public static function model($className=__CLASS__)
	{
	     return parent::model($className);
	}

    public function rules()
    {
        return array(
            array('markup', 'safe'),
            array('name', 'length', 'max' => 60),
            array('name, markup', 'required'),
            array('id', 'safe'),
        );
    }


    public function getEndpoint()
    {
        return '/layouts/';
    }

    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array_merge_recursive(parent::attributeNames(),array(
            'markup',
            'name'
        ));
    }
}