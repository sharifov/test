<?php

namespace modules\hotel\src\exceptions;

use common\CodeExceptionsModule as Module;

class HotelCodeException
{
	public const HOTEL_NOT_FOUND = Module::HOTEL . 100;
	public const HOTEL_SAVE = Module::HOTEL . 101;
	public const HOTEL_REMOVE = Module::HOTEL . 102;

	public const HOTEL_QUOTE_NOT_FOUND = Module::HOTEL . 200;
	public const HOTEL_QUOTE_SAVE = Module::HOTEL . 201;
	public const HOTEL_QUOTE_REMOVE = Module::HOTEL . 202;

	public const HOTEL_LIST_NOT_FOUND = Module::HOTEL . 300;
	public const HOTEL_LIST_SAVE = Module::HOTEL . 301;
	public const HOTEL_LIST_REMOVE = Module::HOTEL . 302;

	public const HOTEL_QUOTE_ROOM_NOT_FOUND = Module::HOTEL . 400;
	public const HOTEL_QUOTE_ROOM_SAVE = Module::HOTEL . 401;
	public const HOTEL_QUOTE_ROOM_REMOVE = Module::HOTEL . 402;

	public const HOTEL_ROOM_NOT_FOUND = Module::HOTEL . 500;
	public const HOTEL_ROOM_SAVE = Module::HOTEL . 501;
	public const HOTEL_ROOM_REMOVE = Module::HOTEL . 502;

	public const HOTEL_ROOM_PAX_NOT_FOUND = Module::HOTEL . 600;
	public const HOTEL_ROOM_PAX_SAVE = Module::HOTEL . 601;
	public const HOTEL_ROOM_PAX_REMOVE = Module::HOTEL . 602;
}
