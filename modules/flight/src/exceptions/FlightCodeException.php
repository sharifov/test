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

    public const FLIGHT_QUOTE_NOT_FOUND = Module::FLIGHT . 300;
    public const FLIGHT_QUOTE_REMOVE = Module::FLIGHT . 301;

    public const FLIGHT_QUOTE_PAX_PRICE_NOT_FOUND = Module::FLIGHT . 400;
    public const FLIGHT_QUOTE_PAX_PRICE_REMOVE = Module::FLIGHT . 401;

    public const FLIGHT_QUOTE_SEGMENT_NOT_FOUND = Module::FLIGHT . 500;
    public const FLIGHT_QUOTE_SEGMENT_REMOVE = Module::FLIGHT . 501;

    public const FLIGHT_QUOTE_TRIP_NOT_FOUND = Module::FLIGHT . 600;
    public const FLIGHT_QUOTE_TRIP_REMOVE = Module::FLIGHT . 601;

    public const FLIGHT_QUOTE_SEGMENT_PAX_BAGGAGE_REMOVE = Module::FLIGHT . 700;
}
