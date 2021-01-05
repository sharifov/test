<?php

namespace sales\model\project\entity\params;

/**
 * Class CaseParams
 *
 * @property bool $sendFeedback
 * @property string $feedbackTemplateTypeKey
 * @property string $feedbackEmailFrom
 * @property string $feedbackNameFrom
 * @property bool $feedbackBookingIdRequired
 */
class CaseParams
{
    public bool $sendFeedback;
    public string $feedbackTemplateTypeKey;
    public string $feedbackEmailFrom;
    public string $feedbackNameFrom;
    public bool $feedbackBookingIdRequired;

    public function __construct(array $params)
    {
        $this->sendFeedback = (bool)($params['sendFeedback'] ?? self::default()['sendFeedback']);
        $this->feedbackTemplateTypeKey = $params['feedbackTemplateTypeKey'] ?? self::default()['feedbackTemplateTypeKey'];
        $this->feedbackEmailFrom = $params['feedbackEmailFrom'] ?? self::default()['feedbackEmailFrom'];
        $this->feedbackNameFrom = $params['feedbackNameFrom'] ?? self::default()['feedbackNameFrom'];
        $this->feedbackBookingIdRequired = (bool)($params['feedbackBookingIdRequired'] ?? self::default()['feedbackBookingIdRequired']);
    }

    public function isActiveFeedback(?string $caseOrderUid): bool
    {
        if ($this->feedbackBookingIdRequired && !$caseOrderUid) {
            return false;
        }
        return $this->sendFeedback && $this->feedbackTemplateTypeKey && $this->feedbackEmailFrom;
    }

    public static function default(): array
    {
        return [
            'sendFeedback' => false,
            'feedbackTemplateTypeKey' => '',
            'feedbackEmailFrom' => '',
            'feedbackNameFrom' => '',
            'feedbackBookingIdRequired' => '',
        ];
    }
}
