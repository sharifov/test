<?php


namespace modules\flight\src\exceptions;

use common\CodeExceptionsModule as Module;

class FlightCodeException
{
	public const FLIGHT_NOT_FOUND = Module::FLIGHT . 100;
	public const FLIGHT_SAVE = Module::FLIGHT . 101;
	public const FLIGHT_REMOVE = Module::FLIGHT . 102;

	public const SEGMENT_NOT_FOUND = Module::FLIGHT . 200;
	public const SEGMENT_SAVE = Module::FLIGHT . 201;
	public const SEGMENT_REMOVE = Module::FLIGHT . 202;
}