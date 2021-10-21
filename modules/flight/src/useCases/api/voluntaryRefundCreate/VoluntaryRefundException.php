<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\CodeExceptionsModule as Module;

class VoluntaryRefundException extends \RuntimeException
{
    public const CASE_CREATION_FAILED = Module::FLIGHT . 101;
    public const CASE_SALE_CREATION_FAILED = Module::FLIGHT . 102;
    public const CLIENT_CREATION_FAILED = Module::FLIGHT . 103;
    public const ORDER_CREATION_FAILED = Module::FLIGHT . 104;
    public const ORIGIN_PRODUCT_QUOTE_CREATION_FAILED = Module::FLIGHT . 105;
    public const PRODUCT_QUOTE_REFUND_CREATION_FAILED = Module::FLIGHT . 106;
    public const PRODUCT_QUOTE_NOT_AVAILABLE_FOR_REFUND = Module::FLIGHT . 107;
}
