<?php

namespace sales\repositories\lead;

use common\models\LeadFlightSegment;
use sales\repositories\NotFoundException;

class LeadSegmentRepository
{
    public function get($id): LeadFlightSegment
    {
        if ($segment = LeadFlightSegment::findOne($id)) {
            return $segment;
        }
        throw new NotFoundException('FlightSegment is not found.');
    }

    public function save(LeadFlightSegment $segment): int
    {
        if ($segment->save(false)) {
            return $segment->id;
        }
        throw new \RuntimeException('Saving error.');
    }

    public function remove(LeadFlightSegment $segment): void
    {
        if (!$segment->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }

    /**
     * @param LeadFlightSegment[] $segments
     * @param array $newIds
     */
    public function removeOld(array $segments, array $newIds): void
    {
        /** @var LeadFlightSegment $segment */
        foreach ($segments as $segment) {
            if (!in_array($segment->id, $newIds)) {
                $this->remove($segment);
            }
        }
    }
}