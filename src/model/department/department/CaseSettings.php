<?php

namespace src\model\department\department;

use src\helpers\setting\SettingHelper;

/**
 * Class CaseSettings
 *
 * @property bool $createOnCall
 * @property bool $createOnSms
 * @property int $trashActiveDaysLimit
 * @property bool $sendFeedback
 * @property string $feedbackTemplateTypeKey
 * @property string $feedbackEmailFrom
 * @property string $feedbackNameFrom
 * @property bool $feedbackBookingIdRequired
 * @property array $createOnDepartmentEmail
 * @property array $createOnPersonalEmail
 */
class CaseSettings
{
    public bool $createOnCall;
    public bool $createOnSms;
    public int $trashActiveDaysLimit;
    public bool $sendFeedback;
    public string $feedbackTemplateTypeKey;
    public string $feedbackEmailFrom;
    public string $feedbackNameFrom;
    public bool $feedbackBookingIdRequired;
    public array $createOnDepartmentEmail;
    public array $createOnPersonalEmail;

    public function __construct(array $params)
    {
        $this->createOnCall = (bool)($params['createOnCall'] ?? false);
        $this->createOnSms = (bool)($params['createOnSms'] ?? false);
        $this->trashActiveDaysLimit = (int)($params['trashActiveDaysLimit'] ?? SettingHelper::getTrashActiveDaysLimitGlobal());
        $this->sendFeedback = (bool)($params['sendFeedback'] ?? false);
        $this->feedbackTemplateTypeKey = (string)($params['feedbackTemplateTypeKey'] ?? '');
        $this->feedbackEmailFrom = (string)($params['feedbackEmailFrom'] ?? '');
        $this->feedbackNameFrom = (string)($params['feedbackNameFrom'] ?? '');
        $this->feedbackBookingIdRequired = (bool)($params['feedbackBookingIdRequired'] ?? false);
        $this->createOnDepartmentEmail = (isset($params['createOnDepartmentEmail']) && is_array($params['createOnDepartmentEmail'])) ? $params['createOnDepartmentEmail'] : [];
        $this->createOnPersonalEmail = (isset($params['createOnPersonalEmail']) && is_array($params['createOnPersonalEmail'])) ? $params['createOnPersonalEmail'] : [];
    }

    public function isActiveFeedback(?string $caseOrderUid): bool
    {
        if ($this->feedbackBookingIdRequired && !$caseOrderUid) {
            return false;
        }
        return $this->sendFeedback && $this->feedbackTemplateTypeKey && $this->feedbackEmailFrom;
    }

    public function isIncludeEmail(string $email): bool
    {
        return in_array($email, $this->createOnDepartmentEmail, true) || in_array($email, $this->createOnPersonalEmail, true);
    }
}
