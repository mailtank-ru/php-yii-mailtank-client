<?php
Yii::setPathOfAlias('mailtank', __DIR__);

use \m8rge\CurlHelper;
use \m8rge\CurlException;

class MailtankClient extends \CApplicationComponent
{
    public $host;
    public $token;

    protected $headers = array();


    public function init()
    {
        $this->headers = array(
            CURLOPT_HTTPHEADER => array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: ' . 'application/json',
            )
        );
    }

    public function sendRequest($endPoint, $fields = array(), $method = 'get')
    {
        $e = null;
        var_dump('Endpoint: ' . 'http://' . $this->host . $endPoint  );
        try {
            if ($method == 'get') {
                $returnedData = CurlHelper::getUrl(
                    'http://' . $this->host . $endPoint . (!empty($fields) ? '?' . http_build_query($fields) : ''),
                    $this->headers
                );
            } else {
                $returnedData = CurlHelper::postUrl('http://' . $this->host . $endPoint, $fields, $this->headers);
            }
        } catch (CurlException $e) {
            $returnedData = $e->getData();
            if (empty($returnedData)) {
                throw $e;
            }
        }
        $answer = json_decode($returnedData, true);
        if (is_null($answer)) {
            throw new MailtankException('answer from mailtank can\'t be decoded: ' . $returnedData, 0, $e);
        }

        return $answer;
    }

}

class MailtankException extends \Exception
{

}
