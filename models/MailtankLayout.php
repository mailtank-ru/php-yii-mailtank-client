<?php
/**
 * Class MailtankLayout
 */
class MailtankLayout extends MailtankRecord
{
    const ENDPOINT = '/layouts/';

    public $name;
    public $markup;
    public $plaintext_markup;
    public $external_id;

    protected $createOnly = true;


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            array('markup', 'safe'),
            array('name', 'length', 'max' => 60),
            array('name, markup', 'required'),
            array('id, external_id, plaintext_markup', 'safe'),
        );
    }

    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array_merge_recursive(parent::attributeNames(), array(
            'markup',
            'name',
            'plaintext_markup',
            'external_id',
        ));
    }
}