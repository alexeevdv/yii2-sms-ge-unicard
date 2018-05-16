<?php

namespace alexeevdv\sms\ge\unicard;

use mikk150\sms\BaseMessage;

/**
 * Class Message
 * @package alexeevdv\sms\ge\unicard
 */
class Message extends BaseMessage
{
    /**
     * @var string
     */
    private $_from;

    /**
     * @var string
     */
    private $_to;

    /**
     * @var string
     */
    private $_body;

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->_from = $from;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @inheritdoc
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->_to = $to;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return $this->_body;
    }
}
