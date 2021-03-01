<?php

namespace modules\order\src\exceptions;

use common\CodeExceptionsModule as Module;

class OrderCodeException
{
    public const ORDER_NOT_FOUND = Module::ORDER . 100;
    public const ORDER_SAVE = Module::ORDER . 101;
    public const ORDER_REMOVE = Module::ORDER . 102;

    public const ORDER_STATUS_LOG_SAVE = Module::ORDER . 110;
    public const ORDER_STATUS_LOG_REMOVE = Module::ORDER . 111;

    public const ORDER_PRODUCT_NOT_FOUND = Module::ORDER . 200;
    public const ORDER_PRODUCT_SAVE = Module::ORDER . 201;
    public const ORDER_PRODUCT_REMOVE = Module::ORDER . 202;

    public const API_ORDER_VIEW_NOT_FOUND_DATA_ON_REQUEST = Module::ORDER . 301;
    public const API_ORDER_VIEW_VALIDATE = Module::ORDER . 302;
    public const API_ORDER_VIEW_NOT_FOUND = Module::ORDER . 303;
}
