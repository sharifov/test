<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\models\FlightQuote;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;

/**
 * Class CaseVoluntaryExchangeHandler
 *
 * @property Cases $case
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class CaseVoluntaryExchangeHandler
{
    private Cases $case;
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param Cases $case
     * @param VoluntaryExchangeObjectCollection $objectCollection
     */
    public function __construct(Cases $case, VoluntaryExchangeObjectCollection $objectCollection)
    {
        $this->case = $case;
        $this->objectCollection = $objectCollection;
    }

    public function addClient(?int $clientId): CaseVoluntaryExchangeHandler
    {
        $this->case->cs_client_id = $clientId;
        $this->objectCollection->getCasesRepository()->save($this->case);
        return $this;
    }

    public function setCaseDeadline(FlightQuote $flightQuote): ?string
    {
        if ($deadline = CaseVoluntaryExchangeService::getCaseDeadline($flightQuote)) {
            $this->case->cs_deadline_dt = $deadline;
            $this->objectCollection->getCasesRepository()->save($this->case);
            $this->case->addEventLog(
                CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                'Set deadline from FlightQuote',
                ['uid' => $flightQuote->fq_uid]
            );
            return $deadline;
        }
        \Yii::warning(
            'CaseDeadline not set by FlightQuote(' . $flightQuote->getId() . ')',
            'CaseVoluntaryExchangeHandler:setCaseDeadline:notSet'
        );
        return null;
    }

    public function getCase(): Cases
    {
        return $this->case;
    }
}
