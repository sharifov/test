<?php

namespace src\model\validators;

use common\CodeExceptionsModule as Module;

class ValidatorsCodeException
{
    public const PHONE_NOT_FOUND = Module::API . 140;
    public const PHONE_NOT_VALID = Module::API . 141;
}
