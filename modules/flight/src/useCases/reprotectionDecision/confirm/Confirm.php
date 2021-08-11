<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class Confirm
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 */
class Confirm
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        ProductQuoteChangeRepository $productQuoteChangeRepository
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
    }

    public function handle(string $reprotectionQuoteGid): void
    {
        $quote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);
        if (!$this->isReprotectionQuoteRelationExist($quote)) {
            throw new \DomainException('Quote is not reprotection quote.');
        }
        if ($quote->isApplied()) {
            throw new \DomainException('Quote is already applied.');
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductQuoteId($quote->pq_id);
        if (!$productQuoteChange->isDecisionPending()) {
            throw new \DomainException('Product Quote Change status is invalid.');
        }

        $this->transactionManager->wrap(function () use ($quote, $productQuoteChange) {
            $this->confirmProductQuoteChange($productQuoteChange);
            $this->markQuoteToApplied($quote);
            $this->cancelOtherReprotectionQuotes($quote);
        });

        $this->createBoRequestJob($quote);
    }

    private function isReprotectionQuoteRelationExist(ProductQuote $quote): bool
    {
        return ProductQuoteRelation::find()->byRelatedQuoteId($quote->pq_id)->reprotection()->exists();
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

    private function cancelOtherReprotectionQuotes(ProductQuote $quote): void
    {
        $parentRelatedId = ProductQuoteRelation::find()->select(['pqr_parent_pq_id'])->byRelatedQuoteId($quote->pq_id)->scalar();
        if (!$parentRelatedId) {
            return;
        }

        $quotes = ProductQuote::find()
            ->andWhere([
                'pq_id' => ProductQuoteRelation::find()
                    ->select(['pqr_related_pq_id'])
                    ->byParentQuoteId((int)$parentRelatedId)
                    ->reprotection()
            ])
            ->all();

        foreach ($quotes as $productQuote) {
            if (!$productQuote->isEqual($quote) && $productQuote->isFlight() && !$productQuote->isCanceled()) {
                $productQuote->cancelled(null, 'Canceled from reProtection');
                $this->productQuoteRepository->save($productQuote);
            }
        }
    }
}
