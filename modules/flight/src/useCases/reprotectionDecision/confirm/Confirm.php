<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
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

    public function handle(string $reprotectionQuoteGid): void
    {
        $reprotectionQuote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);
        if (!$reprotectionQuote->flightQuote->isTypeReProtection()) {
            throw new \DomainException('Quote is not reprotection quote.');
        }
        if ($reprotectionQuote->isApplied()) {
            throw new \DomainException('Quote is already applied.');
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findParentRelated($reprotectionQuote);
        if (!$productQuoteChange->isDecisionPending()) {
            throw new \DomainException('Product Quote Change status is invalid.');
        }

        $this->transactionManager->wrap(function () use ($reprotectionQuote, $productQuoteChange) {
            $this->markQuoteToApplied($reprotectionQuote);
            $this->cancelOtherReprotectionQuotes->cancel($reprotectionQuote);
            $this->confirmProductQuoteChange($productQuoteChange);
        });

        $this->createBoRequestJob($reprotectionQuote);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change): void
    {
        $change->customerDecisionConfirm(null, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
    }

    private function createBoRequestJob(ProductQuote $quote): void
    {
        $boJob = new BoRequestJob();
        $boJob->quoteGid = $quote->pq_gid;
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
