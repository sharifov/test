<?php

namespace sales\model\order;

use common\CodeExceptionsModule as Module;

class OrderCodeException
{
    public const API_ORDER_NOT_FOUND_DATA_ON_REQUEST = Module::ORDER . 100;
}
