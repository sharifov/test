<?php

namespace modules\smartLeadDistribution\src\objects\flight;

use common\models\Lead;
use common\models\LeadFlightSegment;
use src\helpers\DateHelper;

class FlightLeadRatingDto
{
    public int $passengers = 0;
    public int $date_proximity = 0;

    public function __construct(Lead $lead)
    {
        $this->passengers = intval($lead->adults + $lead->children + $lead->infants);

        /** @var LeadFlightSegment $fs */
        $fs = $lead->firstFlightSegment;
        $this->date_proximity = DateHelper::getDifferentInDaysByDatesUTC($fs->departure, date('Y-m-d H:i:s'));
    }
}
