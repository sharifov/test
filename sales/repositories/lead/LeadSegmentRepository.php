<?php

namespace sales\repositories\lead;

use common\models\LeadFlightSegment;
use sales\repositories\NotFoundException;

class LeadSegmentRepository
{
    public function get($id): LeadFlightSegment
    {
        if (!$segment = LeadFlightSegment::findOne($id)) {
            throw new NotFoundException('FlightSegment is not found.');
        }
        return $segment;
    }

    public function save(LeadFlightSegment $segment): void
    {
        if (!$segment->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(LeadFlightSegment $segment): void
    {
        if (!$segment->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}