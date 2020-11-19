<?php

namespace sales\model\department\department;

/**
 * Class LeadSettings
 *
 * @property bool $createOnCall
 * @property bool $createOnSms
 * @property bool $createOnEmail
 */
class LeadSettings
{
    public bool $createOnCall;
    public bool $createOnSms;
    public bool $createOnEmail;

    public function __construct(array $params)
    {
        $this->createOnCall = (bool)($params['createOnCall'] ?? false);
        $this->createOnSms = (bool)($params['createOnSms'] ?? false);
        $this->createOnEmail = (bool)($params['createOnEmail'] ?? false);
    }
}
