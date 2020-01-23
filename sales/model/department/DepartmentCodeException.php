<?php

namespace sales\model\department;

use common\CodeExceptionsModule as Module;

class DepartmentCodeException
{
    public const API_DEPARTMENT_PHONE_PROJECT_GET_NOT_FOUND_DATA_ON_REQUEST = Module::DEPARTMENT . 300;
    public const API_DEPARTMENT_PHONE_PROJECT_GET_VALIDATE = Module::DEPARTMENT . 301;
}
