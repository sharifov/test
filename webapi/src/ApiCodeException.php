<?php

namespace webapi\src;

use common\CodeExceptionsModule as Module;

/**
 * Class ApiCodeException
 */
class ApiCodeException
{
    public const BO_ERROR = Module::API . 100;
    public const NOT_FOUND_PROJECT_CURRENT_USER = Module::API . 101;
    public const NOT_FOUND_PROJECT_CONFIG = Module::API . 108;
    public const EVENT_OR_DATA_IS_NOT_PROVIDED = Module::API . 102;
    public const INTERNAL_SERVER_ERROR = Module::API . 500;
    public const CLIENT_CHAT_REQUEST_CREATE_FAILED = Module::API . 103;
    public const REQUEST_IS_NOT_POST = Module::API . 104;
    public const POST_DATA_IS_EMPTY = Module::API . 105;
    public const POST_DATA_NOT_LOADED = Module::API . 106;
    public const FAILED_FORM_VALIDATE = Module::API . 107;
    public const CLIENT_CHAT_FEEDBACK_CREATE_FAILED = Module::API . 109;
    public const UNEXPECTED_ERROR = Module::API . 110;
    public const GET_DATA_NOT_LOADED = Module::API . 111;
    public const DATA_NOT_FOUND = Module::API . 112;
    public const REQUEST_ALREADY_PROCESSED = Module::API . 113;
    public const REQUEST_DATA_INVALID = Module::API . 114;
    public const DATA_EXPIRED = Module::API . 115;

    public const COMMUNICATION_ERROR = Module::API . 120;

    public const REQUEST_TO_BACK_OFFICE_ERROR = Module::API . 130;

    public const SUCCESS = Module::API . 200;
}
