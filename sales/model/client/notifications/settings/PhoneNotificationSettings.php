<?php

namespace sales\model\client\notifications\settings;

/**
 * Class PhoneNotificationSettings
 *
 * @property $phoneFrom
 * @property $messageSay
 * @property $messageTemplateKey
 * @property $messageSayVoice
 * @property $messageSayLanguage
 * @property $fileUrl
 */
class PhoneNotificationSettings
{
    public $phoneFrom;

    public $messageSay;
    public $messageTemplateKey;
    public $messageSayVoice;
    public $messageSayLanguage;

    public $fileUrl;

    public function __construct($phoneFrom, $messageSay, $messageTemplateKey, $messageSayVoice, $messageSayLanguage, $fileUrl)
    {
        $this->phoneFrom = $phoneFrom;
        $this->messageSay = $messageSay;
        $this->messageTemplateKey = $messageTemplateKey;
        $this->messageSayVoice = $messageSayVoice;
        $this->messageSayLanguage = $messageSayLanguage;
        $this->fileUrl = $fileUrl;
    }
}
