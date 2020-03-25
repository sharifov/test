<?php

namespace sales\model\cases;

use common\CodeExceptionsModule as Module;

class CaseCodeException
{
    public const CASE_NOT_FOUND = Module::CASES . 100;
    public const CASE_SAVE = Module::CASES . 101;
    public const CASE_REMOVE = Module::CASES . 102;

    public const API_CASE_CREATE_NOT_FOUND_DATA_ON_REQUEST = Module::CASES . 300;
    public const API_CASE_CREATE_VALIDATE = Module::CASES . 301;
}
