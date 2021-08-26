<?php

namespace sales\model\project\entity\params;

/**
 * Class SendPhoneNotification
 *
 * @property bool $enabled
 * @property string|null $phoneFrom
 * @property string|null $messageSay
 * @property string $messageSayVoice
 * @property string $messageSayLanguage
 * @property string|null $fileUrl
 * @property string|null $messageTemplateKey
 */
class SendPhoneNotification
{
    public bool $enabled;
    public ?string $phoneFrom;
    public ?string $messageSay;
    public string $messageSayVoice;
    public string $messageSayLanguage;
    public ?string $fileUrl;
    public ?string $messageTemplateKey;

    public function __construct(array $params)
    {
        $this->enabled = array_key_exists('enabled', $params) ? (bool)$params['enabled'] : false;
        $this->phoneFrom = array_key_exists('phoneFrom', $params) ? (string)$params['phoneFrom'] : null;
        $this->messageSay = array_key_exists('messageSay', $params) ? (string)$params['messageSay'] : null;
        $this->messageSayVoice = array_key_exists('messageSayVoice', $params) ? (string)$params['messageSayVoice'] : null;
        $this->messageSayLanguage = array_key_exists('messageSayLanguage', $params) ? (string)$params['messageSayLanguage'] : null;
        $this->fileUrl = array_key_exists('fileUrl', $params) ? (string)$params['fileUrl'] : null;
        $this->messageTemplateKey = array_key_exists('messageTemplateKey', $params) ? (string)$params['messageTemplateKey'] : null;
    }
}
