<?php

namespace modules\hotel\src\exceptions;

use common\CodeExceptionsModule as Module;

class HotelCodeException
{
	public const HOTEL_NOT_FOUND = Module::HOTEL . 100;
	public const HOTEL_SAVE = Module::HOTEL . 101;
	public const HOTEL_REMOVE = Module::HOTEL . 102;
}
