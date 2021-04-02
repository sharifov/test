<?php

namespace modules\flight\src\services\api;

use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\order\src\entities\order\Order;
use sales\repositories\product\ProductQuoteRepository;

/**
 * Class FlightReplaceService
 *
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property ProductQuoteRepository $productQuoteRepository
 */
class FlightReplaceService
{
    private FlightQuoteRepository $flightQuoteRepository;
    private ProductQuoteRepository $productQuoteRepository;

    /**
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param ProductQuoteRepository $productQuoteRepository
     */
    public function __construct(
        FlightQuoteRepository $flightQuoteRepository,
        ProductQuoteRepository $productQuoteRepository
    ) {
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->productQuoteRepository = $productQuoteRepository;
    }
}
