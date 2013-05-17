<?php
/**
 * Class MailtankMailing
 */
class MailtankMailing extends MailtankRecord
{

    const ENDPOINT = '/mailings/';

    public $url;
    public $title;
    public $status;
    public $layout_id;
    public $context;
    public $tags;
    public $subscribers;

    protected $target;

    protected $createOnly = false;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            array('title', 'length', 'max' => 60),
            array('id', 'safe'),
            array('layout_id, context', 'safe'),
            array('tags, subscribers', 'safe'),
            array('title, layout_id, context', 'required'),
        );
    }


    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array_merge_recursive(parent::attributeNames(), array(
            'title',
            'status',
            'tags',
            'url',
            'subscribers',
            'layout_id',
            'context'
        ));
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

    public function beforeSave()
    {
        if ($this->scenario == 'update') {
            throw new MailtankException('Update method is unsupported');
        }
        return parent::beforeSave();
    }


}