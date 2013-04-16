<?php
namespace Mailtank\Models;


use Mailtank\MailtankClient;

/**
 * Class Subscriber
 * @package Mailtank\models
 *
 * @property string email
 * @property int id
 * @property array properties
 * @property array tags
 */
class Subscriber extends MailtankRecord
{
    protected $properties = array();

    public $email;
    public $tags = array();

    public function getEndpoint()
    {
        return 'subscribers';
    }

    public function setProperties($properties)
    {
        if (is_array($properties)) {
            $this->properties = $properties;
        } else {
            throw new \Exception('Type error');
        }
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperty($key, $value)
    {
        if (is_string($key) && !empty($value)) {
            $this->properties[$key] = $value;
        } else {
            throw new \Exception('Type error');
        }
    }

    public function getProperty($key) {
        if(isset($this->properties[$key])) {
            return $this->properties[$key];
        } else {
            return false;
        }
    }

    public static function findByPk($id) {
        $client = MailtankClient::getInstance();



    }
}