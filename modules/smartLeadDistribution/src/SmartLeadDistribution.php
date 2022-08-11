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
}
