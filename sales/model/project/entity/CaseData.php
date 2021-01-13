<?php

namespace sales\model\project\entity;

/**
 * Class CaseData
 *
 * @property bool $sendFeedback
 * @property string $feedbackTemplateTypeKey
 * @property string $feedbackEmailFrom
 * @property string $feedbackNameFrom
 * @property bool $feedbackBookingIdRequired
 */
class CaseData
{
    public bool $sendFeedback;
    public string $feedbackTemplateTypeKey;
    public string $feedbackEmailFrom;
    public string $feedbackNameFrom;
    public bool $feedbackBookingIdRequired;

    public function __construct(array $params)
    {
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
