<?php

namespace alexeevdv\sms\ge\unicard;

/**
 * Interface IDestinationChecker
 * @package alexeevdv\sms\ge\unicard
 */
interface IDestinationChecker
{
    /**
     * Check if message can be sent to provided number
     * @param string $number
     * @return bool
     */
    public function check($number);
}
