<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use DomainException;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightRequest;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\exception\CheckRestrictionException;
use sales\helpers\setting\SettingHelper;
use sales\repositories\cases\CasesRepository;
use Yii;

/**
 * Class CaseReProtectionService
 *
 * @property CasesRepository $casesRepository
 * @property Cases|null $case
 */
class CaseReProtectionService
{
    public const CASE_STATUSES_FOR_SEND = [
        CasesStatus::STATUS_AUTO_PROCESSING,
        CasesStatus::STATUS_FOLLOW_UP,
    ];

    private CasesRepository $casesRepository;
    private ?Cases $case = null;

    /**
     * @param CasesRepository $casesRepository
     */
    public function __construct(
        CasesRepository $casesRepository
    ) {
        $this->casesRepository = $casesRepository;
    }

    public function createCase(FlightRequest $flightRequest): Cases
    {
        if (!$caseCategoryKey = SettingHelper::getReProtectionCaseCategory()) {
            throw new CheckRestrictionException('Setting "reprotection_case_category" is empty');
        }
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => $caseCategoryKey])) {
            throw new CheckRestrictionException('CaseCategory (' . $caseCategoryKey . ') not found');
        }

        $case = Cases::createByApiReProtection(
            $caseCategory->cc_dep_id,
            $caseCategory->cc_id,
            $flightRequest->fr_booking_id,
            $flightRequest->fr_project_id
        );
        $this->setCase($case);
        $this->casesRepository->save($this->case);

        $this->getCase()->addEventLog(
            CaseEventLog::CASE_CREATED,
            'Flight ReProtection Create, BookingID: ' . $flightRequest->fr_booking_id,
            ['case_gid' => $this->getCase()->cs_gid, 'fr_booking_id' => $flightRequest->fr_booking_id]
        );

        return $this->getCase();
    }

    public function additionalFillingCase(?int $clientId, ?int $projectId): Cases
    {
        $this->getCase()->cs_client_id = $clientId;
        $this->getCase()->cs_project_id = $projectId;
        $this->casesRepository->save($this->getCase());
        return $this->getCase();
    }

    public function caseToManual(string $description, ?int $userId = null): Cases
    {
        $this->getCase()->offIsAutomate();
        if (!$this->getCase()->isPending()) {
            $this->getCase()->pending($userId, $description);
        }
        if ($this->getCase()->getDirtyAttributes()) {
            $this->casesRepository->save($this->case);
            $this->getCase()->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, 'Case auto processing: disabled');
        }
        return $this->case;
    }

    public function caseToAutoProcessing(?string $description = null): Cases
    {
        $this->getCase()->onIsAutomate();
        if (!$this->getCase()->isStatusAutoProcessing()) {
            $this->getCase()->autoProcessing(null, $description);
        }
        $this->casesRepository->save($this->getCase());
        $this->getCase()->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, $description);
        return $this->getCase();
    }

    public function setCaseDeadline(FlightQuote $flightQuote): Cases
    {
        if (!(($firstSegment = $flightQuote->flightQuoteSegments[0]) && $firstSegment->fqs_departure_dt)) {
            throw new \RuntimeException('Deadline not created. Reason - Segments departure not correct');
        }
        $schdCaseDeadlineHours = SettingHelper::getSchdCaseDeadlineHours();
        $deadline = date('Y-m-d H:i:s', strtotime($firstSegment->fqs_departure_dt . ' -' . $schdCaseDeadlineHours . ' hours'));

        if ($deadline === false) {
            throw new \RuntimeException('Deadline not created');
        }
        $this->getCase()->cs_deadline_dt = $deadline;
        $this->casesRepository->save($this->getCase());
        return $this->getCase();
    }

    public function isAutomateProcessing(FlightRequest $flightRequest): bool
    {
        return (
            in_array($this->getCase()->cs_status, self::CASE_STATUSES_FOR_SEND, false)
            ||
            ($this->getCase()->isPending() && $flightRequest->getIsAutomateDataJson())
        );
    }

    public static function getLastActiveCaseByBookingId(string $bookingId): ?Cases
    {
        return Cases::find()->where(['cs_order_uid' => $bookingId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]])
            ->innerJoin(CaseCategory::tableName(), 'cs_category_id = cc_id and cc_key = :categoryKey', [
                'categoryKey' => SettingHelper::getReProtectionCaseCategory()
            ])
            ->orderBy(['cs_id' => SORT_DESC])
            ->one();
    }

    public static function existActiveCaseByBookingId(string $bookingId): bool
    {
        return Cases::find()->where(['cs_order_uid' => $bookingId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]])
            ->innerJoin(CaseCategory::tableName(), 'cs_category_id = cc_id and cc_key = :categoryKey', [
                'categoryKey' => SettingHelper::getReProtectionCaseCategory()
            ])->exists();
    }

    public function getCase(): Cases
    {
        if (!$this->case) {
            throw new DomainException('Case is empty');
        }
        return $this->case;
    }

    public function setCase(Cases $case): void
    {
        $this->case = $case;
    }
}
