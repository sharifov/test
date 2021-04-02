<?php

namespace modules\cruise\src\exceptions;

use common\CodeExceptionsModule as Module;

class CruiseCodeException
{
    public const CRUISE_NOT_FOUND = Module::CRUISE . 100;
    public const CRUISE_SAVE = Module::CRUISE . 101;
    public const CRUISE_REMOVE = Module::CRUISE . 102;

    public const CRUISE_QUOTE_NOT_FOUND = Module::CRUISE . 200;
    public const CRUISE_QUOTE_SAVE = Module::CRUISE . 201;
    public const CRUISE_QUOTE_REMOVE = Module::CRUISE . 202;

    public const CRUISE_QUOTE_CABIN_NOT_FOUND = Module::CRUISE . 400;
    public const CRUISE_QUOTE_CABIN_SAVE = Module::CRUISE . 401;
    public const CRUISE_QUOTE_CABIN_REMOVE = Module::CRUISE . 402;

    public const CRUISE_CABIN_NOT_FOUND = Module::CRUISE . 500;
    public const CRUISE_CABIN_SAVE = Module::CRUISE . 501;
    public const CRUISE_CABIN_REMOVE = Module::CRUISE . 502;

    public const CRUISE_CABIN_PAX_NOT_FOUND = Module::CRUISE . 600;
    public const CRUISE_CABIN_PAX_SAVE = Module::CRUISE . 601;
    public const CRUISE_CABIN_PAX_REMOVE = Module::CRUISE . 602;

    public const API_C2B_HANDLE = Module::CRUISE . 901;
}
