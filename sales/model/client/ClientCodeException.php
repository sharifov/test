<?php

namespace sales\model\client;

use common\CodeExceptionsModule as Module;

/**
 * Class ClientCodeException
 * @package sales\model\client
 */
class ClientCodeException
{
    public const CLIENT_NOT_FOUND = Module::CLIENT . 100;
    public const CLIENT_SAVE = Module::CLIENT . 101;
    public const CLIENT_REMOVE = Module::CLIENT . 102;

    public const CLIENT_PHONE_NOT_FOUND = Module::CLIENT . 200;
    public const CLIENT_PHONE_SAVE = Module::CLIENT . 201;
    public const CLIENT_PHONE_REMOVE = Module::CLIENT . 202;

    public const CLIENT_EMAIL_NOT_FOUND = Module::CLIENT . 300;
    public const CLIENT_EMAIL_SAVE = Module::CLIENT . 301;
    public const CLIENT_EMAIL_REMOVE = Module::CLIENT . 302;

    public const INTERNAL_PHONE = Module::CLIENT . 400;
    public const INTERNAL_EMAIL = Module::CLIENT . 401;

    public const CLIENT_CREATE_NOT_ADD_PHONES = Module::CLIENT . 500;
    public const CLIENT_CREATE_NOT_ADD_EMAILS = Module::CLIENT . 501;
    public const CLIENT_PHONES_EMPTY = Module::CLIENT . 502;
    public const CLIENT_EMAILS_EMPTY = Module::CLIENT . 503;
}
