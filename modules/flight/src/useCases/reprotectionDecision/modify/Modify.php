<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\flight\src\useCases\services\cases\CaseService;
use modules\flight\src\useCases\services\productQuote\ProductQuoteService;
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
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\repositories\product\ProductQuoteRepository;
use src\services\TransactionManager;

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
 * @property CaseService $caseService
 * @property ProductQuoteService $productQuoteService
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
    private CaseService $caseService;
    private ProductQuoteService $productQuoteService;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        TransactionManager $transactionManager,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes,
        FlightQuoteManageService $flightQuoteManageService,
        ProductQuoteRelationRepository $productQuoteRelationRepository,
        ProductQuoteDataManageService $productQuoteDataManageService,
        CaseService $caseService,
        ProductQuoteService $productQuoteService
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->transactionManager = $transactionManager;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteRelationRepository = $productQuoteRelationRepository;
        $this->productQuoteDataManageService = $productQuoteDataManageService;
        $this->caseService = $caseService;
        $this->productQuoteService = $productQuoteService;
    }

    public function handle(string $bookingId, array $newQuote, ?int $userId): void
    {
        $originalProductQuote = $this->productQuoteService->getProductQuote($bookingId);

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductIdAndType($originalProductQuote->pq_id, ProductQuoteChange::TYPE_RE_PROTECTION);
        if (!$productQuoteChange->isPending() || !$productQuoteChange->isTypeReProtection()) {
            throw new \DomainException('Product Quote Change status is not in "pending" or is not Schedule Change. Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"; Current Type: "' . $productQuoteChange->getTypeName() . '"', 101);
        }

        $case = $this->caseService->getCase($productQuoteChange, $originalProductQuote);
        if (!$case) {
            \Yii::warning([
                'message' => 'Case not found by Product Quote Change (' . $productQuoteChange->pqc_id . ') or by Product Quote (' . $originalProductQuote->pq_id . ')',
                'originalProductQuote' => $originalProductQuote->pq_id,
                'originalProductQuoteGid' => $originalProductQuote->pq_gid,
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
                'productQuoteChangeGid' => $productQuoteChange->pqc_gid,
            ], 'FlightController:reprotectionDecision:modify:DomainException');
            throw new \DomainException('Case not found by Product Quote Change (' . $productQuoteChange->pqc_id . ') or by Product Quote (' . $originalProductQuote->pq_id . ')');
        }

        $quote = $this->transactionManager->wrap(function () use ($originalProductQuote, $newQuote, $productQuoteChange, $userId, $case) {
            $quote = $this->createNewReprotectionQuote($originalProductQuote, $newQuote, $userId);
            $this->markQuoteToApplied($quote);
            $this->cancelOtherReprotectionQuotes->cancelByQuoteChange($productQuoteChange, $quote, $userId);
            $this->confirmProductQuoteChange($productQuoteChange, $case);
            $this->inProgressProductQuoteChange($productQuoteChange, $case);
            $this->modifyProductQuoteChange($productQuoteChange, $userId, $case);

            $productQuoteChangeRelation = ProductQuoteChangeRelation::create(
                $productQuoteChange->pqc_id,
                $quote->pq_id
            );
            (new ProductQuoteChangeRelationRepository($productQuoteChangeRelation))->save();
            return $quote;
        });

        $this->productQuoteDataManageService->updateRecommendedChangeQuote($originalProductQuote->pq_id, $quote->pq_id);

        $this->createBoRequestJob($quote, $userId, $case);
    }

    private function modifyProductQuoteChange(ProductQuoteChange $change, ?int $userId, Cases $case): void
    {
        $change->customerDecisionModify($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($case->cs_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::MODIFY]);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change, Cases $case): void
    {
        $fromStatus = $change->getSystemStatusName();
        $change->statusToComplete();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($case->cs_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getSystemStatusName(),
            'decided' => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::MODIFY]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function inProgressProductQuoteChange(ProductQuoteChange $change, Cases $case): void
    {
        $fromStatus = $change->getSystemStatusName();
        $change->inProgress();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($case->cs_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getSystemStatusName(),
            'decided'  => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::MODIFY]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function createBoRequestJob(ProductQuote $quote, ?int $userId, Cases $case): void
    {
        $boJob = new BoRequestJob();
        $boJob->quoteGid = $quote->pq_gid;
        $boJob->userId = $userId;
        $boJob->caseId = $case->cs_id;
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
