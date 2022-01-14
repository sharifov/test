<?php

namespace src\model\department\department;

/**
 * Class LeadSettings
 *
 * @property bool $createOnCall
 * @property bool $createOnSms
 * @property array $createOnDepartmentEmail
 * @property array $createOnPersonalEmail
 */
class LeadSettings
{
    public bool $createOnCall;
    public bool $createOnSms;
    public array $createOnDepartmentEmail;
    public array $createOnPersonalEmail;

    public function __construct(array $params)
    {
        $this->createOnCall = (bool)($params['createOnCall'] ?? false);
        $this->createOnSms = (bool)($params['createOnSms'] ?? false);
        $this->createOnDepartmentEmail = (isset($params['createOnDepartmentEmail']) && is_array($params['createOnDepartmentEmail'])) ? $params['createOnDepartmentEmail'] : [];
        $this->createOnPersonalEmail = (isset($params['createOnPersonalEmail']) && is_array($params['createOnPersonalEmail'])) ? $params['createOnPersonalEmail'] : [];
    }

    public function isIncludeEmail(string $email): bool
    {
        return in_array($email, $this->createOnDepartmentEmail, true) || in_array($email, $this->createOnPersonalEmail, true);
    }
}
