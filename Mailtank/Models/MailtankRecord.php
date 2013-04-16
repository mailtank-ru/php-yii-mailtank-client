<?php
namespace Mailtank\Models;

/**
 * Class MailtankRecord
 * @package Mailtank\models
 * @property-read string $endpoint
 */
abstract class MailtankRecord extends \CModel
{
    public $id;
    public $url;

    public function getUrl() {

    }

    abstract public function getEndpoint();

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
}