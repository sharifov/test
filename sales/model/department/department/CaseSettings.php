<?php

namespace sales\model\department\department;

/**
 * Class CaseSettings
 *
 * @property bool $createOnCall
 * @property bool $createOnSms
 * @property bool $createOnEmail
 * @property int $trashActiveDaysLimit
 * @property bool $sendFeedback
 * @property string $feedbackTemplateTypeKey
 * @property string $feedbackEmailFrom
 * @property string $feedbackNameFrom
 * @property bool $feedbackBookingIdRequired
 */
class CaseSettings
{
    public bool $createOnCall;
    public bool $createOnSms;
    public bool $createOnEmail;
    public int $trashActiveDaysLimit;
    public bool $sendFeedback;
    public string $feedbackTemplateTypeKey;
    public string $feedbackEmailFrom;
    public string $feedbackNameFrom;
    public bool $feedbackBookingIdRequired;

    public function __construct(array $params)
    {
        $this->createOnCall = (bool)($params['createOnCall'] ?? false);
        $this->createOnSms = (bool)($params['createOnSms'] ?? false);
        $this->createOnEmail = (bool)($params['createOnEmail'] ?? false);
        $this->trashActiveDaysLimit = (int)($params['trashActiveDaysLimit'] ?? 0);
        $this->sendFeedback = (bool)($params['sendFeedback'] ?? false);
        $this->feedbackTemplateTypeKey = (string)($params['feedbackTemplateTypeKey'] ?? '');
        $this->feedbackEmailFrom = (string)($params['feedbackEmailFrom'] ?? '');
        $this->feedbackNameFrom = (string)($params['feedbackNameFrom'] ?? '');
        $this->feedbackBookingIdRequired = (bool)($params['feedbackBookingIdRequired'] ?? false);
    }

    public function isActiveFeedback(?string $caseOrderUid): bool
    {
        if ($this->feedbackBookingIdRequired && !$caseOrderUid) {
            return false;
        }
        return $this->sendFeedback && $this->feedbackTemplateTypeKey && $this->feedbackEmailFrom;
    }
}
