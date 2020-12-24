<?php

namespace modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository;

use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;

class FlightQuoteSegmentPaxBaggageChargeRepository
{
    /**
     * @param FlightQuoteSegmentPaxBaggageCharge $baggageCharge
     * @return int
     */
    public function save(FlightQuoteSegmentPaxBaggageCharge $baggageCharge): int
    {
        if (!$baggageCharge->save()) {
            throw new \RuntimeException($baggageCharge->getErrorSummary(false)[0]);
        }
        return $baggageCharge->qsbc_id;
    }
}
