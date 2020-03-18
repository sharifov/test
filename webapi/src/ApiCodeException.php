<?php

namespace webapi\src;

use common\CodeExceptionsModule as Module;

class ApiCodeException
{
    public const BO_ERROR = Module::API . 100;
    public const NOT_FOUND_PROJECT_CURRENT_USER = Module::API . 101;
}
