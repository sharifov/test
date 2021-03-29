<?php

namespace modules\order\src\processManager;

class Status
{
    public const NEW = 1;
    public const BOOKING_FLIGHT = 2;
    public const BOOKING_OTHER_PRODUCTS = 3;
    public const BOOKED = 10;
    public const FAILED = 11;
    public const CANCELED = 12;
    public const WAIT_BO_RESPONSE = 13;
    public const FLIGHT_PRODUCT_PROCESSED = 14;

    public const LIST = [
        self::NEW => 'New',
        self::BOOKING_FLIGHT => 'Booking flight',
        self::BOOKING_OTHER_PRODUCTS => 'Booking other products',
        self::BOOKED => 'Booked',
        self::FAILED => 'Failed',
        self::CANCELED => 'Canceled',
        self::WAIT_BO_RESPONSE => 'Wait BO response',
        self::FLIGHT_PRODUCT_PROCESSED => 'Flight product processed',
    ];

    public static function getName(?int $id): string
    {
        return self::LIST[$id] ?? 'undefined';
    }
}
