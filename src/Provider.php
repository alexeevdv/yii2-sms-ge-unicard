<?php

namespace alexeevdv\sms\ge\unicard;

use mikk150\sms\BaseProvider;
use mikk150\sms\MessageInterface;
use yii\base\InvalidConfigException;
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
     * @var int
     */
    private $_messageCounter = 1;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function sendMessage($message)
    {
        $requestData = $this->buildRequestDataForMessage($message);
        $responseData= $this->callApiSendMethod($requestData);
        return !!$this->messagesSent($responseData);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function sendMultiple(array $messages)
    {
        $requestData = [];
        foreach ($messages as $message) {
            $requestData = ArrayHelper::merge($requestData, $this->buildRequestDataForMessage($message));
        }
        $responseData = $this->callApiSendMethod($requestData);
        return $this->messagesSent($responseData);
    }

    /**
     * @param array $requestData
     * @return array
     * @throws InvalidConfigException
     */
    private function callApiSendMethod(array $requestData)
    {
        /** @var HttpClient $httpClient */
        $httpClient = Instance::ensure($this->httpClient, HttpClient::class);

        try {
            $httpResponse = $httpClient->post('Send', $requestData)->send();
        } catch (HttpClientException $e) {
            Instance::ensure('errorHandler')->logException($e);
            return [];
        }

        $responseData = $httpResponse->getData();
        Yii::trace($responseData, __METHOD__);
        return (array) $responseData;
    }

    /**
     * @param array $responseData
     * @return int
     */
    private function messagesSent(array $responseData)
    {
        $successCount = 0;
        $statuses = ArrayHelper::getValue($responseData, 'Statuses', []);
        foreach ($statuses as $status) {
            $successCount += (int)(ArrayHelper::getValue($status, 'Status', false) === '200');
        }
        return $successCount;
    }

    /**
     * @param MessageInterface $message
     * @return array
     */
    private function buildRequestDataForMessage(MessageInterface $message)
    {
        $requestData = ['SMS' => []];
        foreach ((array) $message->getTo() as $to) {
            $requestData['SMS'][] = [
                'ID' => $this->_messageCounter++,
                'Number' => $to,
                'Message' => $message->getBody(),
                'Sender' => $message->getFrom(),
            ];
        }

        return $requestData;
    }
}
