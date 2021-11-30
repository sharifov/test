<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationRepository;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use sales\entities\cases\CaseEventLog;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class Modify
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property TransactionManager $transactionManager
 * @property CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property ProductQuoteRelationRepository $productQuoteRelationRepository
 * @property ProductQuoteDataManageService $productQuoteDataManageService
 */
class Modify
{
    private ProductQuoteRepository $productQuoteRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private TransactionManager $transactionManager;
    private CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes;
    private FlightQuoteManageService $flightQuoteManageService;
    private ProductQuoteRelationRepository $productQuoteRelationRepository;
    private ProductQuoteDataManageService $productQuoteDataManageService;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        TransactionManager $transactionManager,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes,
        FlightQuoteManageService $flightQuoteManageService,
        ProductQuoteRelationRepository $productQuoteRelationRepository,
        ProductQuoteDataManageService $productQuoteDataManageService
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->transactionManager = $transactionManager;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteRelationRepository = $productQuoteRelationRepository;
        $this->productQuoteDataManageService = $productQuoteDataManageService;
    }

    public function handle(string $bookingId, array $newQuote, ?int $userId): void
    {
        $originalProductQuote = $this->getProductQuote($bookingId);

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductQuoteId($originalProductQuote->pq_id);
        if (!$productQuoteChange->isPending() || !$productQuoteChange->isTypeReProtection()) {
            throw new \DomainException('Product Quote Change status is not in "pending" or is not Schedule Change. Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"; Current Type: "' . $productQuoteChange->getTypeName() . '"', 101);
        }

        $quote = $this->transactionManager->wrap(function () use ($originalProductQuote, $newQuote, $productQuoteChange, $userId) {
            $quote = $this->createNewReprotectionQuote($originalProductQuote, $newQuote, $userId);
            $this->markQuoteToApplied($quote);
            $this->cancelOtherReprotectionQuotes->cancelByQuoteChange($productQuoteChange, $userId);
            $this->inProgressProductQuoteChange($productQuoteChange);
            $this->confirmProductQuoteChange($productQuoteChange);
            $this->modifyProductQuoteChange($productQuoteChange, $userId);

            $productQuoteChangeRelation = ProductQuoteChangeRelation::create(
                $productQuoteChange->pqc_id,
                $quote->pq_id
            );
            (new ProductQuoteChangeRelationRepository($productQuoteChangeRelation))->save();
            return $quote;
        });

        $this->productQuoteDataManageService->updateRecommendedChangeQuote($originalProductQuote->pq_id, $quote->pq_id);

        $this->createBoRequestJob($quote, $userId);
    }

    private function getProductQuote(string $bookingId): ProductQuote
    {
        $flight = FlightQuoteFlight::find()->andWhere(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one();
        if (!$flight) {
            throw new \DomainException('Not found Flight Quote Flight. BookingId: ' . $bookingId);
        }
        $productQuote = $flight->fqfFq->fqProductQuote ?? null;
        if ($productQuote) {
            return $productQuote;
        }
        throw new \DomainException('Not found Product Quote. BookingId: ' . $bookingId);
    }

    private function modifyProductQuoteChange(ProductQuoteChange $change, ?int $userId): void
    {
        $change->customerDecisionModify($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::MODIFY]);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change): void
    {
        $fromStatus = $change->getClientStatusName();
        $change->statusToComplete();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getClientStatusName(),
            'decided' => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::MODIFY]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function inProgressProductQuoteChange(ProductQuoteChange $change): void
    {
        $fromStatus = $change->getClientStatusName();
        $change->inProgress();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getClientStatusName(),
            'decided'  => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::MODIFY]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function createBoRequestJob(ProductQuote $quote, ?int $userId): void
    {
        $boJob = new BoRequestJob();
        $boJob->quoteGid = $quote->pq_gid;
        $boJob->userId = $userId;
        $jobId = \Yii::$app->queue_job->push($boJob);
        if (!$jobId) {
            \Yii::error([
                'message' => 'Reprotection decision BO Request Job not created',
                'quoteGid' => $quote->pq_gid,
            ], 'ReprotectionDecision:Modify:BoRequestJob');
        }
    }

    private function markQuoteToApplied(ProductQuote $quote): void
    {
        $quote->applied();
        $this->productQuoteRepository->save($quote);
    }

    private function createNewReprotectionQuote(ProductQuote $originalProductQuote, array $quote, ?int $userId): ProductQuote
    {
        $newQuote = $this->flightQuoteManageService->createReprotectionModify($originalProductQuote, $quote, $originalProductQuote->pq_order_id);
        $relation = ProductQuoteRelation::createReProtection($originalProductQuote->pq_id, $newQuote->pq_id, $userId);
        $this->productQuoteRelationRepository->save($relation);
        return $newQuote;
    }
}
