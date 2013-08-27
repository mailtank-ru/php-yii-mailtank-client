<?php
Yii::import('mailtank.models.*');
Yii::import('mailtank.tests.unit.*');
class MailingTest extends Mailtank_TestCase
{

    public static function createBasicModel()
    {
        // Create subscribers and tags
        $subscribers = array();
        $tags = array('test_tag_' . uniqid());

        for ($i = 2; $i > 0; $i--) {
            $subscriber = SubscriberTest::createBasicModel();
            $subscriber->tags = $tags;
            self::assertTrue($subscriber->save());
            $subscribers[] = $subscriber->id;
        }

        $layout = LayoutTest::createBasicModel();
        $layout->markup = '{{some_var}} {{unsubscribe_link}}';
        self::assertTrue($layout->save(), 'Layout cant be saved');

        $model = new MailtankMailing();
        $model->setAttributes(array(
            'layout_id' => $layout->id,
            'context' => array('some_var' => 'some value'),
            'tags' => $tags,
            'unsubscribe_tags' => $tags,
            'subscribers' => $subscribers,
            'tags_union' => true,
            'tags_and_receivers_union' => true,
            'unsubscribe_tags' => $tags,
        ));

        self::assertTrue($model->validate());

        return $model;
    }


    public function testCreate()
    {
        $model = self::createBasicModel();
        $this->assertTrue($model->save());

        $this->assertContains($model->status, array('ENQUEUED', 'SUCCEEDED', 'FAILED'));
    }

    public function testGetById()
    {
        $savedModel = $this->createBasicModel();
        $this->assertTrue($savedModel->save());

        $model = MailtankMailing::findByPk($savedModel->id);
        $this->assertNotEmpty($model);
    }


    public function testRefresh()
    {
        $savedModel = $this->createBasicModel();

        $e = false;
        try {
            $savedModel->refresh();
        } catch (MailtankException $e) {
            $e = true;
        }
        $this->assertTrue($e, 'Updated model cant be refreshed');
        $this->assertTrue($savedModel->save());
        $this->assertTrue($savedModel->refresh());
    }
}