<?php
Yii::import('mailtank.models.*');
class MailingTest extends Mailtank_TestCase
{

    public static function createBasicModel()
    {
        // Create subscribers and tags
        $subscribers = array();

        for ($i = 2; $i > 0; $i--) {
            $subscriber = SubscriberTest::createBasicModel();
            $subscriber->save();
            $subscribers[] = $subscriber;
        }

        $tags = array('test_tag_' . uniqid());
        $subscriber = SubscriberTest::createBasicModel();
        $subscriber->tags = $tags;
        $subscriber->save();

        $layout = LayoutTest::createBasicModel();
        $layout->markup = '{{some_var}}';
        $layout->save();

        $model = new MailtankMailing();
        $model->setAttributes(array(
            'title' => 'test',
            'layout_id' => $layout->id,
            'context' => array('some_var' => 'some value'),
            'tags' => $tags,
            'subscribers' => $subscribers,
        ));

        return $model;
    }


    public function testCreate()
    {
        $model = self::createBasicModel();
        $this->assertTrue($model->save());

        $this->assertContains($model->status, array('ENQUEUED', 'SUCCEEDED'));
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