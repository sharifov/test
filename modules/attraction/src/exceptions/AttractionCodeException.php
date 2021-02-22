<?php

namespace modules\attraction\src\exceptions;

use common\CodeExceptionsModule as Module;

class AttractionCodeException
{
    public const ATTRACTION_NOT_FOUND = Module::ATTRACTION . 100;
    public const ATTRACTION_SAVE = Module::ATTRACTION . 101;
    public const ATTRACTION_REMOVE = Module::ATTRACTION . 102;
}
