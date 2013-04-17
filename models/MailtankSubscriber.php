<?php
/**
 * Class MailtankSubscriber
 *
 * @property string email
 * @property int id
 * @property array properties
 * @property array tags
 */
class MailtankSubscriber extends MailtankRecord
{
    protected $properties = array();

    public $email;
    public $tags = array();

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            array('email', 'email'),
            array('email', 'length', 'max' => 255),
            array('email', 'required'),
            array('tags, properties', 'safe'),
        );
    }

    public function getEndpoint()
    {
        return '/subscribers/';
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


    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array_merge_recursive(parent::attributeNames(),array(
            'email',
            'tags',
            'url',
            'properties',
        ));
    }
}