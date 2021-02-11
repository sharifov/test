<?php

namespace modules\rentCar\src\exceptions;

use common\CodeExceptionsModule as Module;

class RentCarCodeException
{
    public const RENT_CAR_NOT_FOUND = Module::RENT_CAR . 100;
    public const RENT_CAR_SAVE = Module::RENT_CAR . 101;
    public const RENT_CAR_REMOVE = Module::RENT_CAR . 102;

    public const RENT_CAR_QUOTE_NOT_FOUND = Module::RENT_CAR . 200;
    public const RENT_CAR_QUOTE_SAVE = Module::RENT_CAR . 201;
    public const RENT_CAR_QUOTE_REMOVE = Module::RENT_CAR . 202;
}
