<?php

namespace modules\flight\src\useCases\voluntaryExchange\codeException;

use common\CodeExceptionsModule as Module;

/**
 * Class VoluntaryExchangeCodeException
 */
class VoluntaryExchangeCodeException
{
    public const CASE_CREATION_FAILED = Module::FLIGHT . 401;
    public const CASE_SALE_CREATION_FAILED = Module::FLIGHT . 402;
    public const CLIENT_CREATION_FAILED = Module::FLIGHT . 403;
    public const ORDER_CREATION_FAILED = Module::FLIGHT . 404;
    public const ORIGIN_PRODUCT_QUOTE_CREATION_FAILED = Module::FLIGHT . 405;
}
