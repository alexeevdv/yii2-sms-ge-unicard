<?php

namespace tests\unit;

use alexeevdv\sms\ge\unicard\Message;

/**
 * Class MessageTest
 * @package tests\unit
 */
class MessageTest extends \Codeception\Test\Unit
{
    /**
     * @test
     */
    public function testFrom()
    {
        $message = new Message;
        $message->setFrom('123456');
        $this->assertEquals('123456', $message->getFrom());
    }

    /**
     * @test
     */
    public function testToSingle()
    {
        $message = new Message;
        $message->setTo('123456');
        $this->assertEquals('123456', $message->getTo());
    }

    /**
     * @test
     */
    public function testToMultiple()
    {
        $message = new Message;
        $message->setTo(['1111', '2222']);
        $this->assertEquals(['1111', '2222'], $message->getTo());
    }

    /**
     * @test
     */
    public function testBody()
    {
        $message = new Message;
        $message->setBody('123456');
        $this->assertEquals('123456', $message->getBody());
    }

    /**
     * @test
     */
    public function testToString()
    {
        $message = new Message;
        $message->setBody('123456');
        $this->assertEquals('123456', $message->toString());
    }
}
