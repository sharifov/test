<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use sales\entities\cases\CaseEventLog;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class Confirm
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
 */
class Confirm
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
    }

    public function handle(string $reprotectionQuoteGid, ?int $userId): void
    {
        $reprotectionQuote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);
        if (!$reprotectionQuote->flightQuote->isTypeReProtection()) {
            throw new \DomainException('Quote is not reprotection quote.');
        }
        if ($reprotectionQuote->isApplied()) {
            throw new \DomainException('Quote is already applied.');
        }
        if (!$this->existBookingId($reprotectionQuote)) {
            throw new \DomainException('Not found Booking Id. Quote ID: ' . $reprotectionQuote->pq_id);
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findParentRelated($reprotectionQuote);
        if (!$productQuoteChange->isDecisionPending()) {
            throw new \DomainException('Product Quote Change status is invalid.');
        }

        $this->transactionManager->wrap(function () use ($reprotectionQuote, $productQuoteChange, $userId) {
            $this->markQuoteToApplied($reprotectionQuote);
            $this->cancelOtherReprotectionQuotes->cancel($reprotectionQuote, $userId);
            $this->confirmProductQuoteChange($productQuoteChange, $userId);
        });

        $this->createBoRequestJob($reprotectionQuote, $userId);
    }

    private function existBookingId(ProductQuote $quote): bool
    {
        $lastFlightQuoteFlightBookingId = FlightQuoteFlight::find()->select(['fqf_booking_id'])->andWhere(['fqf_fq_id' => $quote->flightQuote->fq_id])->orderBy(['fqf_id' => SORT_DESC])->scalar();
        if ($lastFlightQuoteFlightBookingId) {
            return true;
        }
        return false;
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change, ?int $userId): void
    {
        $change->customerDecisionConfirm($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::CONFIRM]);
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
            ], 'ReprotectionDecision:Confirm:BoRequestJob');
        }
    }

    private function markQuoteToApplied(ProductQuote $quote): void
    {
        $quote->applied();
        $this->productQuoteRepository->save($quote);
    }
}
