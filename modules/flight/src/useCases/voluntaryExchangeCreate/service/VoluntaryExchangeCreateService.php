<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;

/**
 * Class VoluntaryExchangeCreateService
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeCreateService
{
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->objectCollection = $voluntaryExchangeObjectCollection;
    }
}
