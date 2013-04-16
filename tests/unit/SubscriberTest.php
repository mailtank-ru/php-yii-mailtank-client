<?php
class SubscriberTest extends Mailtank_TestCase {

    public function testAdd() {
        $layout = new \Mailtank\Models\Layout();

        $layout->markup = 'Hello, {{username}}!';
        $layout->name = 'hello';

        $this->client->save($layout);
        $this->assertNotNull($layout->id, "Layout saved and got an id");
    }
}