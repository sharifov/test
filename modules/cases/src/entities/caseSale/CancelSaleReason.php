<?php

namespace modules\cases\src\entities\caseSale;

class CancelSaleReason
{
    public const WRONG_NAME = 1;
    public const PRICE_INCREASE = 2;
    public const DUPLICATE_BOOKING = 3;
    public const NOT_PASS_VERIFICATION = 4;
    public const FOUND_CHEAPER = 5;
    public const DECLINED_CARD_OR_PAYMENT_ISSUES = 6;
    public const ADD_NEW_PASSENGER = 7;
    public const ISSUES_WITH_GOVERNMENT = 8;
    public const ITINERARY_CHANGES = 9;
    public const UNDETERMINED_TRAVEL_DATE = 10;
    public const BAGGAGE_ISSUES = 11;
    public const BOOKING_IS_NOT_ACTUAL = 12;
    public const OTHER = 13;

    public const LIST = [
        self::WRONG_NAME => 'Wrong name',
        self::PRICE_INCREASE => 'Price increase',
        self::DUPLICATE_BOOKING => 'Duplicate booking',
        self::NOT_PASS_VERIFICATION => 'Could not pass the verification',
        self::FOUND_CHEAPER => 'Booked/found cheaper elsewhere',
        self::DECLINED_CARD_OR_PAYMENT_ISSUES => 'Declined Card or Payment issues',
        self::ADD_NEW_PASSENGER => 'Add new passenger to the booking',
        self::ISSUES_WITH_GOVERNMENT => 'Issues with passport or government/visa restrictions',
        self::ITINERARY_CHANGES => 'Wrong itinerary details (different date/O&D/Cabin/Carrier)',
        self::UNDETERMINED_TRAVEL_DATE => 'Undetermined the dates of travel',
        self::BAGGAGE_ISSUES => 'Baggage issues',
        self::BOOKING_IS_NOT_ACTUAL => 'Booking is not actual anymore',
        self::OTHER => 'Other',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(?int $value): string
    {
        return self::LIST[$value] ?? 'Undefined';
    }
}
