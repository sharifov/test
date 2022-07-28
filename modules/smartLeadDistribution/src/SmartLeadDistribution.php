<?php

namespace modules\smartLeadDistribution\src;

use modules\smartLeadDistribution\src\objects\flight\FlightLeadRatingObject;
use modules\smartLeadDistribution\src\objects\flight\FlightSegmentLeadRatingObject;
use modules\smartLeadDistribution\src\objects\lead\LeadLeadRatingObject;

class SmartLeadDistribution
{
    public const OBJ_FLIGHT_SEGMENT = 'flightSegment';
    public const OBJ_LEAD = 'lead';
    public const OBJ_FLIGHT = 'flight';

    public const OBJ_LIST = [
        self::OBJ_FLIGHT_SEGMENT => 'Flight Segment',
        self::OBJ_LEAD => 'Lead',
        self::OBJ_FLIGHT => 'Flight',
    ];

    public const OBJ_CLASS_LIST = [
        self::OBJ_FLIGHT_SEGMENT => FlightSegmentLeadRatingObject::class,
        self::OBJ_LEAD => LeadLeadRatingObject::class,
        self::OBJ_FLIGHT => FlightLeadRatingObject::class,
    ];

    public const CATEGORY_FIRST = 1;
    public const CATEGORY_SECOND = 2;
    public const CATEGORY_THIRD = 3;

    public const CATEGORY_TOTAL_SCORE_LIST = [
        self::CATEGORY_FIRST => [
            'from' => 45,
            'to' => 50,
        ],
        self::CATEGORY_SECOND => [
            'from' => 35,
            'to' => 45,
        ],
        self::CATEGORY_THIRD => [
            'from' => 25,
            'to' => 35,
        ],
    ];

    public const CATEGORY_LIST = [
        self::CATEGORY_FIRST => 'CAT I',
        self::CATEGORY_SECOND => 'CAT II',
        self::CATEGORY_THIRD => 'CAT III',
    ];
}
