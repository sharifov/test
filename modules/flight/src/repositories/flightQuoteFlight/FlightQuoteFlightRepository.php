<?php

namespace modules\flight\src\repositories\flightQuoteFlight;

use modules\flight\models\FlightQuoteFlight;

/**
 * Class FlightQuoteFlightRepository
 */
class FlightQuoteFlightRepository
{
    public function save(FlightQuoteFlight $model): int
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FlightQuoteFlight save failed');
        }
        return $model->fqf_id;
    }

    public function remove(FlightQuoteFlight $model): void
    {
        if (!$model->delete()) {
            throw new \RuntimeException('FlightQuoteFlight remove failed');
        }
    }

    public function removeByFlightQuoteId(int $flightQuoteId): array
    {
        $removedIds = [];
        foreach (FlightQuoteFlight::findAll(['fqf_fq_id' => $flightQuoteId]) as $flightQuoteFlight) {
            $id = $flightQuoteFlight->fqf_id;
            $this->remove($flightQuoteFlight);
            $removedIds[] = $id;
        }
        return $removedIds;
    }
}
