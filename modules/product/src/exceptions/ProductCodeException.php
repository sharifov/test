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
}
