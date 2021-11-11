<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\CodeExceptionsModule as Module;

class VoluntaryRefundCodeException extends \RuntimeException
{
    public const CASE_CREATION_FAILED = Module::FLIGHT . 401;
    public const CASE_SALE_CREATION_FAILED = Module::FLIGHT . 402;
    public const CLIENT_CREATION_FAILED = Module::FLIGHT . 403;
    public const ORDER_CREATION_FAILED = Module::FLIGHT . 404;
    public const ORIGIN_PRODUCT_QUOTE_CREATION_FAILED = Module::FLIGHT . 405;
    public const PRODUCT_QUOTE_REFUND_CREATION_FAILED = Module::FLIGHT . 406;
    public const PRODUCT_QUOTE_NOT_AVAILABLE_FOR_REFUND = Module::FLIGHT . 407;
    public const PAYMENT_DATA_PROCESSED_FAILED = Module::FLIGHT . 408;
    public const BILLING_INFO_PROCESSED_FAILED = Module::FLIGHT . 409;

    public const PRODUCT_QUOTE_NOT_AVAILABLE = Module::FLIGHT . 409;
    public const PRODUCT_QUOTE_NOT_AVAILABLE_CHG_LIST = Module::FLIGHT . 410;
    public const BO_REQUEST_IS_NO_SEND = Module::FLIGHT . 412;
    public const BO_REQUEST_FAILED = Module::FLIGHT . 411;

    public const CRM_SYSTEM_CODES = [
        self::BO_REQUEST_IS_NO_SEND
    ];
}
