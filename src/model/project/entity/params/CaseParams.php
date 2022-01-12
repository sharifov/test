<?php

namespace src\model\project\entity\params;

/**
 * Class CaseParams
 *
 * @property bool $sendFeedback
 * @property string $feedbackTemplateTypeKey
 * @property string $feedbackEmailFrom
 * @property string $feedbackNameFrom
 * @property bool $feedbackBookingIdRequired
 * @property bool $allow_auto_case_create
 */
class CaseParams
{
    public bool $sendFeedback;
    public string $feedbackTemplateTypeKey;
    public string $feedbackEmailFrom;
    public string $feedbackNameFrom;
    public bool $feedbackBookingIdRequired;
    public bool $allow_auto_case_create;

    public function __construct(array $params)
    {
        $this->sendFeedback = (bool)($params['sendFeedback'] ?? self::default()['sendFeedback']);
        $this->feedbackTemplateTypeKey = $params['feedbackTemplateTypeKey'] ?? self::default()['feedbackTemplateTypeKey'];
        $this->feedbackEmailFrom = $params['feedbackEmailFrom'] ?? self::default()['feedbackEmailFrom'];
        $this->feedbackNameFrom = $params['feedbackNameFrom'] ?? self::default()['feedbackNameFrom'];
        $this->feedbackBookingIdRequired = (bool)($params['feedbackBookingIdRequired'] ?? self::default()['feedbackBookingIdRequired']);
        $this->allow_auto_case_create = (bool) ($params['allow_auto_case_create'] ?? self::default()['allow_auto_case_create']);
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
            'allow_auto_case_create' => true,
        ];
    }
}
