<?php

namespace sales\model\department\department;

/**
 * Class CaseSettings
 *
 * @property bool $createOnCall
 * @property bool $createOnSms
 * @property bool $createOnEmail
 * @property int $trashActiveDaysLimit
 */
class CaseSettings
{
    public bool $createOnCall;
    public bool $createOnSms;
    public bool $createOnEmail;
    public int $trashActiveDaysLimit;

    public function __construct(array $params)
    {
        $this->createOnCall = (bool)($params['createOnCall'] ?? false);
        $this->createOnSms = (bool)($params['createOnSms'] ?? false);
        $this->createOnEmail = (bool)($params['createOnEmail'] ?? false);
        $this->trashActiveDaysLimit = (int)($params['trashActiveDaysLimit'] ?? 0);
    }
}
