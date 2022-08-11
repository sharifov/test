<?php

namespace modules\smartLeadDistribution\src\objects\flight;

use common\models\Airports;
use modules\smartLeadDistribution\src\objects\BaseLeadRatingObject;
use modules\smartLeadDistribution\src\objects\LeadRatingObjectInterface;

class FlightSegmentLeadRatingObject extends BaseLeadRatingObject implements LeadRatingObjectInterface
{
    private const NS = 'flight/flightSegment/';

    public const DTO = FlightSegmentLeadRatingDto::class;

    public const OPTGROUP_CALL = 'Flight Segment';

    public const OBJ = 'flightSegment';

    public const FIELD_ORIGIN_COUNTRY       = self::OBJ . '.' . 'origin_country';
    public const FIELD_DESTINATION_COUNTRY    = self::OBJ . '.' . 'destination_country';

    protected const ATTR_FLIGHT_SEGMENT_ORIGIN = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_ORIGIN_COUNTRY,
        'field' => self::FIELD_ORIGIN_COUNTRY,
        'label' => 'Origin country',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];

    protected const ATTR_FLIGHT_SEGMENT_DESTINATION = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_DESTINATION_COUNTRY,
        'field' => self::FIELD_DESTINATION_COUNTRY,
        'label' => 'Destination country',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN],
    ];

    public const ATTRIBUTE_LIST = [
        self::ATTR_FLIGHT_SEGMENT_ORIGIN,
        self::ATTR_FLIGHT_SEGMENT_DESTINATION,
    ];

    public static function getAttributeList(): array
    {
        $attributeList = [];

        $flo = self::ATTR_FLIGHT_SEGMENT_ORIGIN;
        $fld = self::ATTR_FLIGHT_SEGMENT_DESTINATION;

        $flo['values'] = Airports::getCountryList(key: 'a_country_code');
        $fld['values'] = Airports::getCountryList(key: 'a_country_code');

        $attributeList[] = $flo;
        $attributeList[] = $fld;

        return $attributeList;
    }
}
