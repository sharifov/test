<?php

namespace modules\lead\src\services;

use common\models\Lead;
use common\models\LeadFlightSegment;

class LeadFlightRequestService
{
    public const ATTRIBUTE_ORIGIN = 'origin';
    public const ATTRIBUTE_DESTINATION = 'destination';
    public const ATTRIBUTE_DEPARTURE = 'departure';

    private Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function getFlightSegments(): array
    {
        $segments = [];
        foreach ($this->lead->leadFlightSegments as $key => $segment) {
            foreach (self::getAttributesForCheckChanged() as $attribute) {
                $segments[$key][$attribute] = $segment->{$attribute};
            }
        }
        return $segments;
    }

    private static function getAttributesForCheckChanged(): array
    {
        return [self::ATTRIBUTE_ORIGIN, self::ATTRIBUTE_DESTINATION, self::ATTRIBUTE_DEPARTURE];
    }
}
