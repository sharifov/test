<?php

namespace modules\smartLeadDistribution\src\objects\lead;

use common\models\Lead;

class LeadLeadRatingDto
{
    public string $source_cid;
    public string $trip_type;

    public function __construct(Lead $lead)
    {
        $this->source_cid = $lead->source->cid;
        $this->trip_type = $lead->trip_type ?? '';
    }
}
