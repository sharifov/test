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
            return $deadline;
        }
        \Yii::warning(
            'CaseDeadline not set by FlightQuote(' . $flightQuote->getId() . ')',
            'CaseVoluntaryExchangeHandler:setCaseDeadline:notSet'
        );
        return null;
    }

    public function caseToPendingManual(string $description, ?int $userId = null): Cases
    {
        $this->caseToManual()
            ->caseToPending($description, $userId);

        if ($this->case->getDirtyAttributes()) {
            $this->case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, $description);
            $this->objectCollection->getCasesRepository()->save($this->case);
        }
        return $this->case;
    }

    public function caseToPending(string $description, ?int $userId = null): CaseVoluntaryExchangeHandler
    {
        if (!$this->case->isPending()) {
            $this->case->pending($userId, $description);
        }
        return $this;
    }

    public function caseToManual(string $description = 'Case auto processing: disabled'): CaseVoluntaryExchangeHandler
    {
        if ($this->case->isAutomate()) {
            $this->case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, $description);
            $this->case->offIsAutomate();
        }
        return $this;
    }

    public function caseToAutoProcessing(?string $description = 'Automatic processing requested'): Cases
    {
        $this->case->onIsAutomate();
        if (!$this->case->isStatusAutoProcessing()) {
            $this->case->autoProcessing(null, $description);
        }
        $this->objectCollection->getCasesRepository()->save($this->case);
        $this->case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, $description);
        return $this->case;
    }

    public function caseNeedAction(): Cases
    {
        if (!$this->case->isNeedAction()) {
            $this->case->onNeedAction();
            $this->objectCollection->getCasesRepository()->save($this->case);
        }
        return $this->case;
    }
}
