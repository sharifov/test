<?php

namespace modules\product\src\exceptions;

use common\CodeExceptionsModule as Module;

class ProductCodeException
{
    public const PRODUCT_NOT_FOUND = Module::PRODUCT . 100;
    public const PRODUCT_SAVE = Module::PRODUCT . 101;
    public const PRODUCT_REMOVE = Module::PRODUCT . 102;

    public const PRODUCT_FLIGHT_UNAVAILABLE = Module::PRODUCT . 200;
    public const PRODUCT_HOTEL_UNAVAILABLE = Module::PRODUCT . 201;

    public const INVALID_PRODUCT_TYPE_PRODUCTABLE_REPOSITORY = Module::PRODUCT . 300;
    public const INVALID_PRODUCT_TYPE_GUARD = Module::PRODUCT . 301;
    public const INVALID_PRODUCT_TYPE_FACTORY = Module::PRODUCT . 302;

    public const PRODUCT_QUOTE_STATUS_LOG_SAVE = Module::PRODUCT . 400;
    public const PRODUCT_QUOTE_STATUS_LOG_REMOVE = Module::PRODUCT . 401;

    public const PRODUCT_QUOTE_NOT_FOUND = Module::PRODUCT . 410;
    public const PRODUCT_QUOTE_SAVE = Module::PRODUCT . 411;
    public const PRODUCT_QUOTE_REMOVE = Module::PRODUCT . 412;

    public const PRODUCT_OPTION_NOT_FOUND = Module::PRODUCT . 500;
    public const PRODUCT_OPTION_SAVE = Module::PRODUCT . 501;
    public const PRODUCT_OPTION_REMOVE = Module::PRODUCT . 502;

    public const PRODUCT_QUOTE_OPTION_NOT_FOUND = Module::PRODUCT . 600;
    public const PRODUCT_QUOTE_OPTION_SAVE = Module::PRODUCT . 601;
    public const PRODUCT_QUOTE_OPTION_REMOVE = Module::PRODUCT . 602;

    public const PRODUCT_TYPE_NOT_FOUND = Module::PRODUCT . 700;
    public const PRODUCT_TYPE_SAVE = Module::PRODUCT . 701;
    public const PRODUCT_TYPE_REMOVE = Module::PRODUCT . 702;
}
