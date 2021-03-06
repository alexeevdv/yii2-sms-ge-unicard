<?php

namespace tests\unit;

use alexeevdv\sms\ge\unicard\GeorgiaDestinationChecker;
use alexeevdv\sms\ge\unicard\Message;
use alexeevdv\sms\ge\unicard\Provider;
use Codeception\Stub;
use Yii;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Request as HttpRequest;
use yii\httpclient\Response as HttpResponse;
use yii\web\ErrorHandler;

/**
 * Class ProviderTest
 * @package tests\unit
 */
class ProviderTest extends \Codeception\Test\Unit
{
    /**
     * @inheritdoc
     */
    public function _after()
    {
        Yii::$container->clear('errorHandler');
    }

    /**
     * @test
     */
    public function testSendWhenProviderNotAvailable()
    {
        // Exception should be properly handled and logged
        $provider = new Provider([
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    throw new HttpClientException;
                },
            ]),
        ]);

        Yii::$container->set('errorHandler', function () {
            return Stub::make(ErrorHandler::class, [
                'logException' => Stub\Expected::once(),
            ]);
        });

        $this->assertFalse($provider->send($this->getMessage()));
    }

    /**
     * @test
     */
    public function testSuccessfulSend()
    {
        $provider = new Provider([
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    return Stub::make(HttpRequest::class, [
                        'send' => function () {
                            return Stub::make(HttpResponse::class, [
                                'getData' => [
                                    'Statuses' => [
                                        [
                                            'ID' => 1,
                                            'Number' => '1234567890',
                                            'ResultMessage' => 'Operation is Successful',
                                            'Status' => '200',
                                        ]
                                    ],
                                ],
                            ]);
                        },
                    ]);
                },
            ])
        ]);

        $this->assertTrue($provider->send($this->getMessage()));
    }

    /**
     * @test
     */
    public function testNotSuccessfulSend()
    {
        $provider = new Provider([
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    return Stub::make(HttpRequest::class, [
                        'send' => function () {
                            return Stub::make(HttpResponse::class, [
                                'getData' => [
                                    'Statuses' => [
                                        [
                                            'ID' => 1,
                                            'Number' => '1234567890',
                                            'ResultMessage' => 'Operation is Failed',
                                            'Status' => '104',
                                        ]
                                    ],
                                ],
                            ]);
                        },
                    ]);
                },
            ])
        ]);

        $this->assertFalse($provider->send($this->getMessage()));
    }

    /**
     * @test
     */
    public function testSendWithMalformedResponseData()
    {
        $provider = new Provider([
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    return Stub::make(HttpRequest::class, [
                        'send' => function () {
                            return Stub::make(HttpResponse::class, [
                                'getData' => 'Hurrdurr',
                            ]);
                        },
                    ]);
                },
            ])
        ]);

        $this->assertFalse($provider->send($this->getMessage()));
    }

    /**
     * @test
     */
    public function testSendMultiple()
    {
        $provider = new Provider([
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    return Stub::make(HttpRequest::class, [
                        'send' => function () {
                            return Stub::make(HttpResponse::class, [
                                'getData' => [
                                    'Statuses' => [
                                        [
                                            'ID' => 1,
                                            'Number' => '222',
                                            'ResultMessage' => 'Operation is Successful',
                                            'Status' => '200',
                                        ],
                                        [
                                            'ID' => 2,
                                            'Number' => '555',
                                            'ResultMessage' => 'Operation is Failed',
                                            'Status' => '104',
                                        ],
                                        [
                                            'ID' => 3,
                                            'Number' => '666',
                                            'ResultMessage' => 'Operation is Successful',
                                            'Status' => '200',
                                        ],
                                    ],
                                ],
                            ]);
                        },
                    ]);
                },
            ])
        ]);

        $messages = [
            Stub::make(Message::class, [
                'getFrom' => '444',
                'getTo' => ['555', '666'],
                'getBody' => '777'
            ]),
            $this->getMessage(),
        ];

        $this->assertEquals(2, $provider->sendMultiple($messages));
    }

    /**
     * @test
     */
    public function testSuccessfulSendWithDestinationChecker()
    {
        $provider = new Provider([
            'destinationChecker' => GeorgiaDestinationChecker::class,
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    return Stub::make(HttpRequest::class, [
                        'send' => function () {
                            return Stub::make(HttpResponse::class, [
                                'getData' => [
                                    'Statuses' => [
                                        [
                                            'ID' => 1,
                                            'Number' => '1234567890',
                                            'ResultMessage' => 'Operation is Successful',
                                            'Status' => '200',
                                        ]
                                    ],
                                ],
                            ]);
                        },
                    ]);
                },
            ])
        ]);

        $message = Stub::make(Message::class, [
            'getFrom' => '111',
            'getTo' => '+995595123123',
            'getBody' => '333',
        ]);
        $this->assertTrue($provider->send($message));
    }

    /**
     * @test
     */
    public function testNotSuccessfulSendWithDestinationChecker()
    {
        $provider = new Provider([
            'destinationChecker' => GeorgiaDestinationChecker::class,
            'httpClient' => Stub::make(HttpClient::class, [
                'post' => function () {
                    return Stub::make(HttpRequest::class, [
                        'send' => Stub\Expected::never(),
                    ]);
                },
            ])
        ]);

        $message = Stub::make(Message::class, [
            'getFrom' => '111',
            'getTo' => 'asdf',
            'getBody' => '333',
        ]);
        $this->assertFalse($provider->send($message));
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        return Stub::make(Message::class, [
            'getFrom' => '111',
            'getTo' => '222',
            'getBody' => '333',
        ]);
    }
}
