<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class Confirm
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 */
class Confirm
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;

    public function __construct(ProductQuoteRepository $productQuoteRepository, TransactionManager $transactionManager)
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
    }

    public function handle(string $reprotectionQuoteGid): void
    {
        $quote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);

        $this->transactionManager->wrap(function () use ($quote) {
            $this->markQuoteToApplied($quote);
            $this->cancelOtherReprotectionQuotes($quote);
        });

        $this->createBoRequestJob($quote);
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
        if ($quote->isApplied()) {
            throw new \DomainException('Quote is already applied.');
        }
        $quote->applied();
        $this->productQuoteRepository->save($quote);
    }

    private function cancelOtherReprotectionQuotes(ProductQuote $quote): void
    {
        $parentRelatedId = ProductQuoteRelation::find()->select(['pqr_parent_pq_id'])->andWhere(['pqr_related_pq_id' => $quote->pq_id])->scalar();
        if (!$parentRelatedId) {
            return;
        }

        $quotes = ProductQuote::find()
            ->andWhere([
                'pq_id' => ProductQuoteRelation::find()
                    ->select(['pqr_related_pq_id'])
                    ->andWhere([
                        'pqr_parent_pq_id' => (int)$parentRelatedId,
                        'pqr_type_id' => ProductQuoteRelation::TYPE_REPROTECTION
                    ])
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
