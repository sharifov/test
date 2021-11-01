<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use webapi\src\logger\behaviors\filters\creditCard\V4;
use webapi\src\logger\behaviors\filters\Filterable;

/**
 * Class CleanDataVoluntaryExchangeService
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property FlightRequest $flightRequest
 * @property ProductQuoteChange|null $productQuoteChange
 * @property Filterable $creditCardFilter
 */
class CleanDataVoluntaryExchangeService
{
    private VoluntaryExchangeObjectCollection $objectCollection;
    private FlightRequest $flightRequest;
    private ?ProductQuoteChange $productQuoteChange = null;
    private Filterable $creditCardFilter;

    /**
     * @param FlightRequest $flightRequest
     * @param ProductQuoteChange|null $productQuoteChange
     * @param VoluntaryExchangeObjectCollection $objectCollection
     */
    public function __construct(
        FlightRequest $flightRequest,
        ?ProductQuoteChange $productQuoteChange,
        VoluntaryExchangeObjectCollection $objectCollection
    ) {
        $this->objectCollection = $objectCollection;
        $this->flightRequest = $flightRequest;
        $this->productQuoteChange = $productQuoteChange;
        $this->creditCardFilter = new CreditCardFilter();

        $this->processing();
    }

    public function processing(): void
    {
        $this->cleanFlightRequest()->cleanProductQuoteChange();
    }

    private function cleanFlightRequest(): CleanDataVoluntaryExchangeService
    {
        $frDataJson = $this->creditCardFilter->filterData($this->flightRequest->fr_data_json);
        $this->flightRequest->fr_data_json = $frDataJson;
        $this->objectCollection->getFlightRequestRepository()->save($this->flightRequest);
        return $this;
    }

    private function cleanProductQuoteChange(): CleanDataVoluntaryExchangeService
    {
        if (!empty($this->productQuoteChange)) {
            $pqcDataJson = $this->creditCardFilter->filterData($this->productQuoteChange->pqc_data_json);
            $this->productQuoteChange->pqc_data_json = $pqcDataJson;
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);
        }
        return $this;
    }
}
