<?php

namespace alexeevdv\sms\ge\unicard;

use mikk150\phonevalidator\PhoneNumberValidator;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class GeorgiaDestinationChecker
 * @package alexeevdv\sms\ge\unicard
 */
class GeorgiaDestinationChecker implements IDestinationChecker
{
    /**
     * @param string $number
     * @return bool
     * @throws InvalidConfigException
     */
    public function check($number)
    {
        $validator = Yii::createObject([
            'class' => PhoneNumberValidator::class,
            'country' => 'GE',
        ]);
        return $validator->validate($number);
    }
}
