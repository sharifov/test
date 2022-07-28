<?php

namespace modules\smartLeadDistribution\src\objects\flight;

use common\models\Airports;
use common\models\Lead;
use common\models\LeadFlightSegment;

class FlightSegmentLeadRatingDto
{
    public ?string $origin_country = null;
    public ?string $destination_country = null;

    public function __construct(Lead $lead)
    {
        /** @var LeadFlightSegment $ffs */
        $ffs = $lead->firstFlightSegment;
        $airportOrigin = Airports::findByIata($ffs->origin);
        $airportDestination = Airports::findByIata($ffs->origin);

        if ($airportOrigin !== null) {
            $this->origin_country = $airportOrigin->country;
        }

        if ($airportDestination !== null) {
            $this->destination_country = $airportDestination->country;
        }
    }
}
