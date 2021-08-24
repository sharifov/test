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
 */
class Modify
{
    private ProductQuoteRepository $productQuoteRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private TransactionManager $transactionManager;
    private CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes;
    private FlightQuoteManageService $flightQuoteManageService;
    private ProductQuoteRelationRepository $productQuoteRelationRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        TransactionManager $transactionManager,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes,
        FlightQuoteManageService $flightQuoteManageService,
        ProductQuoteRelationRepository $productQuoteRelationRepository
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->transactionManager = $transactionManager;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteRelationRepository = $productQuoteRelationRepository;
    }

    public function handle(string $bookingId, array $newQuote, ?int $userId): void
    {
        $originalProductQuote = $this->getProductQuote($bookingId);

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductQuoteId($originalProductQuote->pq_id);
        if (!$productQuoteChange->isDecisionPending()) {
            throw new \DomainException('Product Quote Change status is not in "Decision pending". Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"');
        }

        $quote = $this->transactionManager->wrap(function () use ($originalProductQuote, $newQuote, $productQuoteChange, $userId) {
            $quote = $this->createNewReprotectionQuote($originalProductQuote, $newQuote, $userId);
            $this->markQuoteToApplied($quote);
            $this->cancelOtherReprotectionQuotes->cancel($quote, $userId);
            $this->modifyProductQuoteChange($productQuoteChange, $userId);
            return $quote;
        });

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
        $newQuote = $this->flightQuoteManageService->createReprotectionModify($originalProductQuote->flightQuote->fqFlight, $quote, $originalProductQuote->pq_order_id);
        $relation = ProductQuoteRelation::createReProtection($originalProductQuote->pq_id, $newQuote->pq_id, $userId);
        $this->productQuoteRelationRepository->save($relation);
        return $newQuote;
    }
}
