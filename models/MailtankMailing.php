<?php
/**
 * Class MailtankMailing
 */
class MailtankMailing extends MailtankRecord
{

    const ENDPOINT = '/mailings/';

    public $url;
    public $status;
    public $layout_id;
    public $context;
    public $tags;
    public $tags_union = false;
    public $tags_and_receivers_union = false;
    public $unsubscribe_tags;
    public $unsubscribe_link;
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
            array('id', 'safe'),
            array('layout_id, context', 'safe'),
            array('tags, subscribers', 'safe'),
            array('layout_id, context', 'required'),
            array('unsubscribe_link', 'url'),
            array('unsubscribe_tags', 'unsubscribeTagValidator'),
            array('tags_union, tags_and_receivers_union', 'type', 'type' => 'bool'),
        );
    }

    public function unsubscribeTagValidator($attribute, $params) {
        if(empty($this->{$attribute}) && empty($this->unsubscribe_link)) {
            $this->addError($attribute,
                'Unsubscribe tags is required if no unsubscribe link specified');
        }
    }


    /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array_merge_recursive(parent::attributeNames(), array(
            'status',
            'tags',
            'tags_union',
            'tags_and_receivers_union',
            'unsubscribe_tags',
            'url',
            'subscribers',
            'layout_id',
            'context'
        ));
    }

    public function beforeSendAttributes($fields)
    {
        function move_param($param, & $fields)
        {
            if(empty($fields[$param])) {
                return;
            }
            $fields['target'][$param] = $fields[$param];
            unset($fields[$param]);
        }

        move_param('tags', $fields);
        move_param('unsubscribe_tags', $fields);
        move_param('unsubscribe_link', $fields);
        move_param('subscribers', $fields);
        move_param('tags_union', $fields);
        move_param('tags_and_receivers_union', $fields);

        return parent::beforeSendAttributes($fields);
    }

    public function beforeSave()
    {
        if ($this->scenario == 'update') {
            throw new MailtankException('Update method is unsupported');
        }
        return parent::beforeSave();
    }


}