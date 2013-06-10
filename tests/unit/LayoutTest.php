<?php
class LayoutTest extends Mailtank_TestCase
{
    public static function createBasicModel()
    {
        $model = new MailtankLayout();
        $id = uniqid();
        $model->setAttributes(array(
            'id' => $id,
            'name' => 'test',
            'markup' => 'Hello, {{username}}!',
            'subject_markup' => 'Hello, {{username}}!',
        ));

        return $model;
    }

    public function testCreate()
    {

        $layout = self::createBasicModel();
        $unsavedModel = clone $layout;

        $this->assertTrue($layout->save());

        $this->assertEquals($unsavedModel->id, $layout->id);
        $this->assertEquals('test', $layout->name);
        $this->assertEquals('Hello, {{username}}!', $layout->markup);
        $this->assertEquals('Hello, {{username}}!', $layout->subject_markup);
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