<?php
namespace Mailtank\Models;


/**
 * Class Layout
 * @package Mailtank\Models
 */
class Layout extends MailtankRecord {
    public $markup;
    public $name;

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
        return 'layouts/';
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