<?php

namespace modules\smartLeadDistribution\src\objects\flight;

use modules\smartLeadDistribution\src\objects\BaseLeadRatingObject;
use modules\smartLeadDistribution\src\objects\LeadRatingObjectInterface;

class FlightLeadRatingObject extends BaseLeadRatingObject implements LeadRatingObjectInterface
{
    private const NS = 'flight/';

    public const DTO = FlightLeadRatingDto::class;

    public const OPTGROUP_CALL = 'Flight';

    public const OBJ = 'flight';

    public const FIELD_PASSENGERS = self::OBJ . '.' . 'passengers';
    public const FIELD_DATE_PROXIMITY = self::OBJ . '.' . 'date_proximity';

    protected const ATTR_PASSENGERS = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_PASSENGERS,
        'field' => self::FIELD_PASSENGERS,
        'label' => 'Number of passengers',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, '<', '>', '<=', '>=']
    ];

    protected const ATTR_DATE_PROXIMITY = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_DATE_PROXIMITY,
        'field' => self::FIELD_DATE_PROXIMITY,
        'label' => 'Travel date proximity',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, '<', '>', '<=', '>=']
    ];

    public const ATTRIBUTE_LIST = [
        self::ATTR_PASSENGERS,
        self::ATTR_DATE_PROXIMITY,
    ];
}
