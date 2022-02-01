<?php

namespace src\model\leadPoorProcessing\service\rules;

use common\models\EmailTemplateType;
use yii\helpers\ArrayHelper;

/**
 * Class LeadPoorProcessingNoAction
 */
class LeadPoorProcessingNoAction extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    public const SMS_TPL_CLIENT_OFFER_KEY = 'sms_client_offer';
    public const SMS_TPL_CLIENT_OFFER_VIEW_KEY = 'sms_client_offer_view';
    public const SMS_TPL_PRODUCT_OFFER_VIEW_KEY = 'sms_product_offer';

    public const SMS_TPL_OFFER_LIST = [
        self::SMS_TPL_CLIENT_OFFER_KEY => self::SMS_TPL_CLIENT_OFFER_KEY,
        self::SMS_TPL_CLIENT_OFFER_VIEW_KEY => self::SMS_TPL_CLIENT_OFFER_VIEW_KEY,
        self::SMS_TPL_PRODUCT_OFFER_VIEW_KEY => self::SMS_TPL_PRODUCT_OFFER_VIEW_KEY,
    ];

    public function checkCondition(): bool
    {
        if (!$this->getRule()->isEnabled()) {
            throw new \RuntimeException('Rule (' . $this->getRule()->lppd_key . ') not enabled');
        }
        if (!$this->getLead()->isProcessing()) {
            throw new \RuntimeException('Lead (' . $this->getLead()->id . ') not in status "processing"');
        }
        return true;
    }

    public static function checkSmsTemplate(?string $template): bool
    {
        return in_array($template, self::SMS_TPL_OFFER_LIST, true);
    }

    public static function checkEmailTemplate(?string $templateKey): bool
    {
        if (!$tpl = EmailTemplateType::find()->where(['etp_key' => $templateKey])->limit(1)->one()) {
            throw new \RuntimeException('EmailTemplateType not found by(' . $templateKey . ')');
        }
        return (bool) ArrayHelper::getValue($tpl->etp_params_json, 'quotes.selectRequired', false);
    }
}
