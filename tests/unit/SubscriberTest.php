<?php
class SubscriberTest extends Mailtank_TestCase
{

    protected function createBasicModel()
    {
        $model = new MailtankSubscriber();
        $external_id = 'id' . uniqid();
        $model->setAttributes(array(
            'external_id' => $external_id,
            'email' => $external_id . "@example.com",
        ));

        return $model;
    }


    public function testCreate()
    {
        $subscriber = $this->createBasicModel();

        $subscriber->tags = array('test1', 'test2');

        $subscriber->setProperties(array(
            'property1' => 1,
            'property2' => 0,
            'property3' => 3,
        ));
        $subscriber->setProperty('property2', 2);

        $unsavedModel = clone $subscriber;
        $this->assertTrue($subscriber->save());

        $this->assertEquals($subscriber->external_id, $unsavedModel->external_id);
        $this->assertEquals($subscriber->email, $unsavedModel->email);

        $this->assertContains(
            array(
                'property1' => 1,
                'property2' => 2,
                'property3' => 3,
            ),
            $subscriber->attributes
        );

        $this->assertContains(
            array(
                'test1',
                'test2',
            ),
            $subscriber->attributes
        );

        $this->assertNotNull($subscriber->id);
    }

    public function testGetById()
    {
        $savedModel = $this->createBasicModel();
        $this->assertTrue($savedModel->save());

        $subscriber = MailtankSubscriber::findByPk($savedModel->id);
        $this->assertEquals($savedModel->attributes, $subscriber->attributes);
    }

    public function testGetByExternalId()
    {
        $savedModel = $this->createBasicModel();
        $this->assertTrue($savedModel->save());

        $subscriber = MailtankSubscriber::findByPk($savedModel->external_id);
        $this->assertEquals($savedModel->attributes, $subscriber->attributes);
    }

    public function testUpdate() {
        $savedModel = $this->createBasicModel();
        $savedModel->tags = array('test1', 'test2');

        $savedModel->setProperties(array(
            'property1' => 1,
            'property2' => 0,
            'property3' => 3,
        ));
        $savedModel->setProperty('property2', 2);
        $this->assertTrue($savedModel->save());

        $model = clone $savedModel;

        $newExternalId = 'id' . uniqid();
        $newEmail = $newExternalId . '@example.com';

        $model->setProperty('property2', 2);
        $model->setProperty('property4', 4);
        $model->tags = array('test2', 'test3');
        $model->external_id = $newExternalId;
        $model->email = $newEmail;

        $this->assertTrue($model->save());



        /** @var $_model MailtankSubscriber */
        foreach (array($model, $model::findByPk($newExternalId)) as $_model) {
            $this->assertEquals($newExternalId, $_model->external_id);
            $this->assertEquals($newEmail, $_model->email);

            $this->assertContains(
                array(
                    'property1' => 1,
                    'property2' => 2,
                    'property3' => 3,
                    'property4' => 4,
                ),
                $_model->attributes
            );

            $this->assertContains(
                array(
                    'test2',
                    'test3',
                ),
                $_model->attributes
            );
        }
    }

    public function testDelete() {
        $model = $this->createBasicModel();
        $this->assertTrue($model->save());

        $this->assertTrue($model->delete());
        $this->assertFalse(MailtankSubscriber::findByPk($model->id));
    }
}