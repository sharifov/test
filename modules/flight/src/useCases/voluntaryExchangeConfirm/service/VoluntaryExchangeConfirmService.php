<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\service;

use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;

/**
 * Class VoluntaryExchangeConfirmService
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeConfirmService
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
