<?php

namespace modules\flight\src\repositories\flightPaxRepository;

use modules\flight\models\FlightPax;
use sales\repositories\NotFoundException;

class FlightPaxRepository
{
    /**
     * @param FlightPax $flightPax
     * @return int
     */
    public function save(FlightPax $flightPax): int
    {
        if (!$flightPax->save()) {
            throw new \RuntimeException($flightPax->getErrorSummary(false)[0]);
        }
        return $flightPax->fp_id;
    }

    public function findByUid(string $uid): FlightPax
    {
        if ($pax = FlightPax::findOne(['fp_uid' => $uid])) {
            return $pax;
        }
        throw new NotFoundException('Flight pax not found by uid: ' . $uid);
    }

    public function remove(FlightPax $flightPax): void
    {
        if (!$flightPax->delete()) {
            throw new \RuntimeException('FlightPax remove failed');
        }
    }

    public function removePaxByFlight(int $flightId): array
    {
        $removedResult = [];
        foreach (FlightPax::findAll(['fp_flight_id' => $flightId]) as $flightPax) {
            $flightPaxId = $flightPax->fp_id;
            $this->remove($flightPax);
            $removedResult[] = $flightPaxId;
        }
        return $removedResult;
    }
}
