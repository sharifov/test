<?php

namespace modules\flight\src\repositories\flightQuotePaxPriceRepository;

use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\entities\flightQuotePaxPrice\FlightQuotePaxPriceQuery;
use modules\flight\src\exceptions\AttractionCodeException;
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
        throw new NotFoundException('Flight Quote Pax Price is not found', AttractionCodeException::FLIGHT_QUOTE_PAX_PRICE_NOT_FOUND);
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
            throw new \RuntimeException('Removing error', AttractionCodeException::FLIGHT_QUOTE_PAX_PRICE_REMOVE);
        }
    }
}
