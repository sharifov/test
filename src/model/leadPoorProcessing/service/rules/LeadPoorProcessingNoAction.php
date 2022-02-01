<?php

namespace src\model\leadPoorProcessing\service\rules;

/**
 * Class LeadPoorProcessingNoAction
 */
class LeadPoorProcessingNoAction extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    public const SMS_TPL_CLIENT_OFFER_KEY = 'sms_client_offer';
    public const SMS_TPL_OFFER_LIST = [
        self::SMS_TPL_CLIENT_OFFER_KEY => self::SMS_TPL_CLIENT_OFFER_KEY,
    ];

    public const EMAIL_TPL_PRODUCT_OFFER_KEY = 'cl_product_offer';
    public const EMAIL_TPL_OFFER_LIST = [
        self::EMAIL_TPL_PRODUCT_OFFER_KEY => self::EMAIL_TPL_PRODUCT_OFFER_KEY,
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
}
