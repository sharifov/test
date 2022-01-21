<?php

namespace src\dto\email;

/**
 * Class EmailConfigsDTO
 * @package src\dto\email
 *
 * @property bool $enabled
 * @property string $emailFrom
 * @property string $emailFromName
 * @property string $templateTypeKey
 */
class EmailConfigsDTO
{
    public bool $enabled = false;

    public string $emailFrom = '';

    public string $emailFromName = '';

    public string $templateTypeKey = '';

    public function __construct(array $emailConfigs)
    {
        $this->enabled = (bool)($emailConfigs['enabled'] ?? false);
        $this->emailFrom = $emailConfigs['emailFrom'] ?? '';
        $this->emailFromName = $emailConfigs['emailFromName'] ?? '';
        $this->templateTypeKey = $emailConfigs['templateTypeKey'] ?? '';

        if (!$this->emailFrom) {
            throw new \RuntimeException('Email Configs: emailFrom is empty');
        }

        if (!$this->emailFromName) {
            throw new \RuntimeException('Email Configs: emailFromName is empty');
        }

        if (!$this->templateTypeKey) {
            throw new \RuntimeException('Email Configs: templateTypeKey is empty');
        }
    }
}
