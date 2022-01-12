<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\models\FlightQuote;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\exception\CheckRestrictionException;
use src\helpers\setting\SettingHelper;

/**
 * Class CaseVoluntaryExchangeService
 */
class CaseVoluntaryExchangeService
{
    public const CASE_CREATE_CATEGORY_KEY = 'voluntary_exchange';

    public static function getCategoryKey(): string
    {
        if (!empty(SettingHelper::getVoluntaryExchangeCaseCategory())) {
            return SettingHelper::getVoluntaryExchangeCaseCategory();
        }
        return self::CASE_CREATE_CATEGORY_KEY;
    }

    public static function createCase(
        string $bookingId,
        int $projectId,
        ?bool $isAutomate,
        VoluntaryExchangeObjectCollection $objectCollection
    ): Cases {
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => self::getCategoryKey()])) {
            throw new CheckRestrictionException('CaseCategory (' . self::getCategoryKey() . ') not found');
        }

        $case = Cases::createByApiVoluntaryExChange(
            $caseCategory->cc_dep_id,
            $caseCategory->cc_id,
            $bookingId,
            $projectId,
            $isAutomate
        );
        $objectCollection->getCasesRepository()->save($case);

        $case->addEventLog(
            CaseEventLog::CASE_CREATED,
            'Voluntary Exchange Create, BookingID: ' . $bookingId,
            ['case_gid' => $case->cs_gid, 'fr_booking_id' => $bookingId]
        );

        return $case;
    }

    public static function getLastActiveCaseByBookingId(string $bookingId): ?Cases
    {
        return Cases::find()->where(['cs_order_uid' => $bookingId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]])
            ->innerJoin(CaseCategory::tableName(), 'cs_category_id = cc_id and cc_key = :categoryKey', [
                'categoryKey' => self::getCategoryKey()
            ])
            ->orderBy(['cs_id' => SORT_DESC])
            ->one();
    }

    public static function getCaseDeadline(FlightQuote $flightQuote): ?string
    {
        foreach ($flightQuote->flightQuoteTrips as $key => $trip) {
            if (!(($firstSegment = $trip->flightQuoteSegments[0]) && $firstSegment->fqs_departure_dt)) {
                throw new \RuntimeException('Deadline not created. Reason - Segments departure not correct');
            }
            $curTime = new \DateTime('now', new \DateTimeZone('UTC'));
            $departureTime = new \DateTime($firstSegment->fqs_departure_dt, new \DateTimeZone('UTC'));

            if ($curTime <= $departureTime) {
                $schdCaseDeadlineHours = SettingHelper::getSchdCaseDeadlineHours();
                return $departureTime->modify(' -' . $schdCaseDeadlineHours . ' hours')->format('Y-m-d H:i:s');
            }
        }
        return null;
    }
}
