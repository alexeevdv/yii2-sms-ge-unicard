<?php

namespace alexeevdv\sms\ge\unicard;

use mikk150\sms\BaseProvider;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Exception as HttpClientException;
use Yii;

/**
 * Class Provider
 * @package alexeevdv\sms\ge\unicard
 */
class Provider extends BaseProvider
{
    /**
     * @var array|string|HttpClient
     */
    public $httpClient = [
        'class' => HttpClient::class,
        'baseUrl' => 'http://192.168.5.80:9090/SMS.svc/rest',
    ];

    /**
     * @inheritdoc
     */
    public $messageClass = Message::class;

    /**
     * @inheritdoc
     */
    public $useFileTransport = false;

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        /** @var HttpClient $httpClient */
        $httpClient = Instance::ensure($this->httpClient, HttpClient::class);

        $requestData = ['SMS' => []];
        $requestData['SMS'][] = [
            'ID' => time(),
            'Number' => $message->getTo(),
            'Message' => $message->getBody(),
            'Sender' => $message->getFrom(),
        ];

        try {
            $httpResponse = $httpClient->post('Send', $requestData)->send();
        } catch (HttpClientException $e) {
            Yii::$app->errorHandler->logException($e);
            return false;
        }
        $responseData = $httpResponse->getData();
        Yii::trace($responseData, __METHOD__);
        $status = ArrayHelper::getValue($responseData, 'Statuses.0.Status', false);
        return $status === '200';
    }
}
