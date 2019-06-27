<?php

namespace sales\repositories\lead;

use common\models\LeadFlightSegment;
use sales\repositories\NotFoundException;

class LeadSegmentRepository
{
    /**
     * @param $id
     * @return LeadFlightSegment
     */
    public function get($id): LeadFlightSegment
    {
        if ($segment = LeadFlightSegment::findOne($id)) {
            return $segment;
        }
        throw new NotFoundException('FlightSegment is not found.');
    }

    /**
     * @param LeadFlightSegment $segment
     * @return int
     */
    public function save(LeadFlightSegment $segment): int
    {
        if ($segment->save(false)) {
            return $segment->id;
        }
        throw new \RuntimeException('Saving error.');
    }

    /**
     * @param LeadFlightSegment $segment
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadFlightSegment $segment): void
    {
        if (!$segment->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }

    /**
     * @param array $segments
     * @param array $newIds
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
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