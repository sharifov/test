<?php

namespace src\model\project\entity\params;

/**
 * Class SendEmailNotification
 *
 * @property bool $enabled
 * @property string|null $emailFrom
 * @property string|null $emailFromName
 * @property string|null $messageTemplateKey
 */
class SendEmailNotification
{
    public bool $enabled;
    public ?string $emailFrom;
    public ?string $emailFromName;
    public ?string $messageTemplateKey;

    public function __construct(array $params)
    {
        $this->enabled = array_key_exists('enabled', $params) ? (bool)$params['enabled'] : false;
        $this->emailFrom = array_key_exists('emailFrom', $params) ? (string)$params['emailFrom'] : null;
        $this->emailFromName = array_key_exists('emailFromName', $params) ? (string)$params['emailFromName'] : null;
        $this->messageTemplateKey = array_key_exists('messageTemplateKey', $params) ? (string)$params['messageTemplateKey'] : null;
    }
}
