<?php

namespace modules\product\exceptions;

use common\CodeExceptionsModule as Module;

class ProductCodeException
{
    public const PRODUCT_NOT_FOUND = Module::PRODUCT . 100;
    public const PRODUCT_SAVE = Module::PRODUCT . 101;
    public const PRODUCT_REMOVE = Module::PRODUCT . 102;
}
