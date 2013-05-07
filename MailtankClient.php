<?php
Yii::setPathOfAlias('mailtank', __DIR__);

class MailtankClient extends \CApplicationComponent
{
    public $host;
    public $token;

    protected $headers = array();


    public function init()
    {
        $this->headers = array(
            'X-Auth-Token' => $this->token,
            'Content-Type' => 'application/json',
        );
    }

    public function sendRequest($endPoint, $fields = array(), $method = 'get')
    {
        spl_autoload_unregister(array('YiiBase', 'autoload'));
        require_once 'requests/library/Requests.php';
        Requests::register_autoloader();

        switch ($method) {
            case 'get':
                $response = Requests::get(
                    'http://' . $this->host . $endPoint . (!empty($fields) ? '?' . http_build_query($fields) : ''),
                    $this->headers
                );
                $returnedData = json_decode($response->body, true);
                break;
            case 'delete':
                $response = Requests::delete('http://' . $this->host . $endPoint, $this->headers);
                $returnedData = $response->body;
                break;
            default:
                $response = Requests::$method('http://' . $this->host . $endPoint, $this->headers, $fields);
                $returnedData = json_decode($response->body, true);
                break;
        }

        spl_autoload_unregister(array('Requests', 'autoloader'));
        spl_autoload_register(array('YiiBase', 'autoload'));

        if (!$response->success) {
            $message = @json_decode($response->body);
            if (!empty($message->message)) {
                $message = $message->message;
            }
            throw new MailtankException("Request failed at url: $method {$response->url}. " . $message, $response->status_code);
        }

        if (is_null($returnedData)) {
            throw new MailtankException('answer from mailtank can\'t be decoded: ' . $response->body);
        }


        return $returnedData;
    }

}

class MailtankException extends \Exception
{

}
