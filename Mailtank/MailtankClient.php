<?php
namespace Mailtank;


use Mailtank\Models\MailtankRecord;
use Guzzle\Http\Client;


class MailtankClient
{
    protected $host;
    protected $api_key;
    protected $client;

    function __construct($host, $api_key)
    {
        $this->api_key = $api_key;
        $this->host = $host;

        if(!$this->client) {
            $this->client = new Client();
        }

        $this->client->setBaseUrl($this->host);
        $this->client->setDefaultHeaders(
            array(
                'X-Auth-Token' => $this->api_key,
                'Content-Type' => 'application/json',
            )
        );

    }
    public function save(MailtankRecord &$record)
    {
        $method = 'PUT';
        $postData = $record->attributes;
        if (!$record->id) {
            unset($postData['id']);
            $method = 'POST';
        }

        switch ($method) {
            case 'POST':
                $request = $this->client->post(
                    $record->endpoint,
                    null,
                    json_encode($postData)
                );
                break;
            case 'PUT':
                $request = $this->client->put(
                    $record->url,
                    null,
                    json_encode($postData)
                );
                break;
        }


        $data = $request->send()->json();
        $record->setAttributes($data);
    }

}
