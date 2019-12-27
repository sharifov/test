<?php

namespace sales\model\lead;

use common\CodeExceptionsModule as Module;

class LeadCodeException
{
    public const NOT_FOUND = Module::LEAD . 1;
    public const SAVE = Module::LEAD . 2;
    public const REMOVE = Module::LEAD . 3;
    public const UPDATE_TRIP_TYPE = Module::LEAD . 4;
}
