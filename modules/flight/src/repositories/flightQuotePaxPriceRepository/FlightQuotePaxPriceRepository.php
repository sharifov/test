<?php

namespace modules\flight\src\repositories\flightQuotePaxPriceRepository;

use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\entities\flightQuotePaxPrice\FlightQuotePaxPriceQuery;
use modules\flight\src\exceptions\FlightCodeException;
use sales\repositories\NotFoundException;

/**
 * Class FlightQuotePaxPriceRepository
 * @package modules\flight\src\repositories\flightQuotePaxPriceRepository
 */
class FlightQuotePaxPriceRepository
{
    /**
     * @param int $fqId
     * @param int $paxCode
     * @return array|FlightQuotePaxPrice|null
     */
    public function findByIdAndCode(int $fqId, int $paxCode)
    {
        $flightQuotePaxPrice = FlightQuotePaxPriceQuery::findByFlightIdAndPaxCodeId($fqId, $paxCode);
        if (!$flightQuotePaxPrice) {
            throw new \RuntimeException('Pax Price not found');
        }
        return $flightQuotePaxPrice;
    }

    public function find(int $id): FlightQuotePaxPrice
    {
        if ($paxPrice = FlightQuotePaxPrice::findOne($id)) {
            return $paxPrice;
        }
        throw new NotFoundException('Flight Quote Pax Price is not found', FlightCodeException::FLIGHT_QUOTE_PAX_PRICE_NOT_FOUND);
    }

    /**
     * @param FlightQuotePaxPrice $flightQuotePaxPrice
     * @return int
     */
    public function save(FlightQuotePaxPrice $flightQuotePaxPrice): int
    {
        if (!$flightQuotePaxPrice->save()) {
            throw new \RuntimeException($flightQuotePaxPrice->getErrorSummary(false)[0]);
        }
        return $flightQuotePaxPrice->qpp_id;
    }

    public function remove(FlightQuotePaxPrice $paxPrice): void
    {
        if (!$paxPrice->delete()) {
            throw new \RuntimeException('Removing error', FlightCodeException::FLIGHT_QUOTE_PAX_PRICE_REMOVE);
        }
    }

    public function removeByFlightQuoteId(int $flightQuoteId): array
    {
        $removedIds = [];
        foreach (FlightQuotePaxPrice::findAll(['qpp_flight_quote_id' => $flightQuoteId]) as $model) {
            $id = $model->qpp_id;
            $this->remove($model);
            $removedIds[] = $id;
        }
        return $removedIds;
    }
}
