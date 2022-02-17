<?php

namespace src\model\leadPoorProcessing\entity;

/**
* @see LeadPoorProcessingDictionary
*/
class LeadPoorProcessingDictionary
{
    public const SMS_TPL_CLIENT_OFFER_KEY = 'sms_client_offer';
    public const SMS_TPL_CLIENT_OFFER_VIEW_KEY = 'sms_client_offer_view';
    public const SMS_TPL_PRODUCT_OFFER_VIEW_KEY = 'sms_product_offer';

    public const SMS_TPL_OFFER_LIST = [
        self::SMS_TPL_CLIENT_OFFER_KEY => self::SMS_TPL_CLIENT_OFFER_KEY,
        self::SMS_TPL_CLIENT_OFFER_VIEW_KEY => self::SMS_TPL_CLIENT_OFFER_VIEW_KEY,
        self::SMS_TPL_PRODUCT_OFFER_VIEW_KEY => self::SMS_TPL_PRODUCT_OFFER_VIEW_KEY,
    ];
}
