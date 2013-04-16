<?php
use \Mailtank\MailtankClient;


class Mailtank_TestCase extends PHPUnit_Framework_TestCase {
    /**
     * @var $client MailtankClient
     */
    protected $client;

    protected function setUp()
    {
        if (is_null($this->client)) {
            $params = require(__DIR__ . '/params.php');
            $this->client = new MailtankClient($params['host'], $params['token']);
        }
    }
}