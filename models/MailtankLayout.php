<?php
/**
 * Class MailtankLayout
 */
class MailtankLayout extends MailtankRecord
{
    public $markup;
    public $name;

    protected $createOnly = false;

    public static function listStatuses()
    {
        return array(
            0 => 'NOT_ENQUEUED',
            1 => 'ENQUEUED',
            2 => 'FAILED',
            3 => 'SUCCEEDED'
        );
    }

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
            array('external_id', 'safe'),
            array('id', 'safe'),
        );
    }


    public static function getEndpoint()
    {
        return '/layouts/';
    }

    public function beforeSendAttributes($fields)
    {
        $tags = $fields['tags'];
        $subscribers = $fields['subscribers'];

        unset($fields['tags'], $fields['subscribers']);

        if (!empty($tags)) {
            $fields['target']['tags'] = $tags;
        }

        if (!empty($subscribers)) {
            $fields['target']['subscribers'] = $subscribers;
        }

        return $fields;
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
            'external_id',
            'status'
        ));
    }
}