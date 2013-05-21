<?php
class LayoutTest extends Mailtank_TestCase
{
    public static function createBasicModel()
    {
        $model = new MailtankLayout();
        $external_id = uniqid();
        $model->setAttributes(array(
            'external_id' => $external_id,
            'name' => 'test',
            'markup' => 'Hello, {{username}}!',
        ));

        return $model;
    }

    public function testCreate()
    {

        $layout = self::createBasicModel();
        $unsavedModel = clone $layout;

        $this->assertTrue($layout->save());

        $this->assertNotNull($layout->id, "Layout saved and got an id");
        $this->assertEquals($unsavedModel->external_id, $layout->external_id);
        $this->assertEquals('test', $layout->name);
        $this->assertEquals('Hello, {{username}}!', $layout->markup);

        $this->id = $layout->id;
    }

    public function testGetById()
    {
        $savedModel = self::createBasicModel();
        $this->assertTrue($savedModel->save());

        try {
            MailtankLayout::findByPk($savedModel->id);
        } catch (MailtankException $e) {
            return;
        }
        $this->fail('Layout cant be retrieved by id');
    }

    public function testGetByExternalId()
    {
        $savedModel = self::createBasicModel();
        $this->assertTrue($savedModel->save());

        try {
            MailtankLayout::findByExternalPk($savedModel->external_id);
        } catch (MailtankException $e) {
            return;
        }
        $this->fail('Layout cant be retrieved by external id');
    }

    public function testUpdate()
    {
        $layout = self::createBasicModel();
        $this->assertTrue($layout->save());

        try {
            $layout->save();
        } catch (MailtankException $e) {
            return;
        }
        $this->fail('Layout cant be saved');
    }

    public function testDelete()
    {
        $model = self::createBasicModel();
        $this->assertTrue($model->save());

        try {
            $model->delete();
        } catch (MailtankException $e) {
            return true;
        }

        $this->fail('Layout cant be deleted');
    }

    public function testRefresh()
    {
        $savedModel = $this->createBasicModel();
        $this->assertTrue($savedModel->save());

        try {
            $savedModel->refresh();
        } catch (MailtankException $e) {
            return;
        }

        $this->fail('Layout cant be refreshed');
    }
}