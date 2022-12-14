<?php

namespace src\model\project\entity\params;

/**
 * Class SendSmsNotification
 *
 * @property bool $enabled
 * @property string|null $phoneFrom
 * @property string|null $nameFrom
 * @property string|null $messageTemplateKey
 */
class SendSmsNotification
{
    public bool $enabled;
    public ?string $phoneFrom;
    public ?string $nameFrom;
    public ?string $messageTemplateKey;

    public function __construct(array $params)
    {
        $this->enabled = array_key_exists('enabled', $params) ? (bool)$params['enabled'] : false;
        $this->phoneFrom = array_key_exists('phoneFrom', $params) ? (string)$params['phoneFrom'] : null;
        $this->nameFrom = array_key_exists('nameFrom', $params) ? (string)$params['nameFrom'] : null;
        $this->messageTemplateKey = array_key_exists('messageTemplateKey', $params) ? (string)$params['messageTemplateKey'] : null;
    }
}
