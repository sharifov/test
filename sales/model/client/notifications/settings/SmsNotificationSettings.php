<?php

namespace sales\model\client\notifications\settings;

/**
 * Class SmsNotificationSettings
 *
 * @property $phoneFrom
 * @property $nameFrom
 * @property $message
 * @property $messageTemplateKey
 */
class SmsNotificationSettings
{
    public $phoneFrom;
    public $nameFrom;
    public $message;
    public $messageTemplateKey;

    public function __construct($phoneFrom, $nameFrom, $message, $messageTemplateKey)
    {
        $this->phoneFrom = $phoneFrom;
        $this->nameFrom = $nameFrom;
        $this->message = $message;
        $this->messageTemplateKey = $messageTemplateKey;
    }
}
