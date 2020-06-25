<?php

namespace webapi\src;

use common\CodeExceptionsModule as Module;

class ApiCodeException
{
    public const BO_ERROR = Module::API . 100;
    public const NOT_FOUND_PROJECT_CURRENT_USER = Module::API . 101;
    public const EVENT_OR_DATA_IS_NOT_PROVIDED = Module::API . 102;
    public const INTERNAL_SERVER_ERROR = Module::API . 500;
    public const CLIENT_CHAT_REQUEST_CREATE_FAILED = Module::API . 103;
    public const REQUEST_IS_NOT_POST = Module::API . 104;
    public const POST_DATA_IS_EMPTY = Module::API . 105;
}
