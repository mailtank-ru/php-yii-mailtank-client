<?php
Yii::import('mailtank.models.*');
Yii::import('mailtank.tests.unit.*');

class MailingTest extends Mailtank_TestCase
{
    private static $subscribers = array();
    private static $layoutId = false;


    public static function createBasicModel()
    {
        // Create subscribers and tags
        $tags = array('test_tag_' . uniqid());

        for ($i = 2; $i > 0; $i--) {
            $subscriber = SubscriberTest::createBasicModel();
            $subscriber->tags = $tags;
            self::assertTrue($subscriber->save());
            self::$subscribers[] = $subscriber->id;
        }

        $layout = LayoutTest::createBasicModel();
        $layout->markup = '{{some_var}} {{unsubscribe_link}}';
        $layout->subject_markup = 'Hello';
        self::assertTrue($layout->save(), 'Layout cant be saved');
        self::$layoutId = $layout->id;

        $model = new MailtankMailing();
        $model->setAttributes(array(
            'layout_id'         => $layout->id,
            'context'           => array('some_var' => 'some value'),
            'tags'              => $tags,
            'subscribers'       => self::$subscribers,
            'unsubscribe_tags'  => $tags,
//            'tags_union' => true,
//            'tags_and_receivers_union' => true,
//            'unsubscribe_link'
//            'attachments'
        ));

        self::assertTrue($model->validate());

        return $model;
    }

    private function clearUnusedData()
    {
        foreach (self::$subscribers as $subscriberId) {
            $subscriber = MailtankSubscriber::model()->findByPk($subscriberId);
            $this->assertTrue($subscriber->delete());
        }
        self::$subscribers = array();

        if (self::$layoutId !== false) {
            $layout = new MailtankLayout();
            $layout->id = self::$layoutId;
            $this->assertTrue($layout->delete());
            self::$layoutId = false;
        }
    }

    public function testCreate()
    {
        $model = self::createBasicModel();
        $this->assertTrue($model->save());

        $this->assertContains($model->status, array('ENQUEUED', 'SUCCEEDED', 'FAILED'));
        $this->clearUnusedData();
    }

    public function testGetById()
    {
        $savedModel = self::createBasicModel();
        $this->assertTrue($savedModel->save());

        $model = MailtankMailing::findByPk($savedModel->id);
        $this->assertNotEmpty($model);
        $this->clearUnusedData();
    }


    public function testRefresh()
    {
        $savedModel = self::createBasicModel();

        $e = false;
        try {
            $savedModel->refresh();
        } catch (MailtankException $e) {
            $e = true;
        }
        $this->assertTrue($e, 'Updated model cant be refreshed');
        $this->assertTrue($savedModel->save());
        $this->assertTrue($savedModel->refresh());
        $this->clearUnusedData();
    }
}