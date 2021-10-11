<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

use modules\flight\models\FlightQuote;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\exception\CheckRestrictionException;
use sales\helpers\setting\SettingHelper;

/**
 * Class VoluntaryExchangeCaseService
 *
 * @property Cases $case
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeCaseService
{
    public const CASE_CATEGORY_KEY = 'exchange';

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

    public function addClient(?int $clientId): VoluntaryExchangeCaseService
    {
        $this->case->cs_client_id = $clientId;
        $this->objectCollection->getCasesRepository()->save($this->case);
        return $this;
    }

    public static function createCase(
        string $bookingId,
        int $projectId,
        VoluntaryExchangeObjectCollection $objectCollection
    ): Cases {
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => self::CASE_CATEGORY_KEY])) {
            throw new CheckRestrictionException('CaseCategory (' . self::CASE_CATEGORY_KEY . ') not found');
        }

        $case = Cases::createByApiVoluntaryExChange(
            $caseCategory->cc_dep_id,
            $caseCategory->cc_id,
            $bookingId,
            $projectId
        );
        $objectCollection->getCasesRepository()->save($case);

        $case->addEventLog(
            CaseEventLog::CASE_CREATED,
            'Voluntary Exchange Create, BookingID: ' . $bookingId,
            ['case_gid' => $case->cs_gid, 'fr_booking_id' => $bookingId]
        );

        return $case;
    }

    public function setCaseDeadline(FlightQuote $flightQuote): ?string
    {
        foreach ($flightQuote->flightQuoteTrips as $key => $trip) {
            if (!(($firstSegment = $trip->flightQuoteSegments[0]) && $firstSegment->fqs_departure_dt)) {
                throw new \RuntimeException('Deadline not created. Reason - Segments departure not correct');
            }
            $curTime = new \DateTime('now', new \DateTimeZone('UTC'));
            $departureTime = new \DateTime($firstSegment->fqs_departure_dt, new \DateTimeZone('UTC'));

            if ($curTime <= $departureTime) {
                $schdCaseDeadlineHours = SettingHelper::getSchdCaseDeadlineHours();
                $deadline = $departureTime->modify(' -' . $schdCaseDeadlineHours . ' hours')->format('Y-m-d H:i:s');
                $this->case->cs_deadline_dt = $deadline;
                $this->objectCollection->getCasesRepository()->save($this->case);
                $this->case->addEventLog(
                    CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                    'Set deadline from FlightQuote',
                    ['uid' => $flightQuote->fq_uid]
                );
                return $deadline;
            }
        }
        \Yii::warning(
            'CaseDeadline not set by FlightQuote(' . $flightQuote->getId() . ')',
            'VoluntaryExchangeCaseService:setCaseDeadline:notSet'
        );
        return null;
    }

    public static function getLastActiveCaseByBookingId(string $bookingId): ?Cases
    {
        return Cases::find()->where(['cs_order_uid' => $bookingId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]])
            ->innerJoin(CaseCategory::tableName(), 'cs_category_id = cc_id and cc_key = :categoryKey', [
                'categoryKey' => self::CASE_CATEGORY_KEY
            ])
            ->orderBy(['cs_id' => SORT_DESC])
            ->one();
    }

    public function getCase(): Cases
    {
        return $this->case;
    }
}
