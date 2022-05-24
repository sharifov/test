<?php

namespace src\model\lead;

use common\CodeExceptionsModule as Module;

class LeadCodeException
{
    public const LEAD_NOT_FOUND = Module::LEAD . 100;
    public const LEAD_SAVE = Module::LEAD . 101;
    public const LEAD_REMOVE = Module::LEAD . 102;
    public const LEAD_UPDATE_TRIP_TYPE = Module::LEAD . 103;

    public const SEGMENT_NOT_FOUND = Module::LEAD . 200;
    public const SEGMENT_SAVE = Module::LEAD . 201;
    public const SEGMENT_REMOVE = Module::LEAD . 202;

    public const API_LEAD_NOT_FOUND_DATA_ON_REQUEST = Module::LEAD . 300;
    public const API_LEAD_VALIDATE = Module::LEAD . 301;

    public const LEAD_USER_CONVERSATION_NOT_PARAM = Module::LEAD . 401;
    public const LEAD_USER_CONVERSATION_NOT_FOUND = Module::LEAD . 402;
    public const LEAD_ACCESS_DENIED = Module::LEAD . 403;
}
