<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\repositories\product\ProductQuoteRepository;

/**
 * Class Confirm
 *
 * @property ProductQuoteRepository $productQuoteRepository
 */
class Confirm
{
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(ProductQuoteRepository $productQuoteRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
    }

    public function handle(string $reprotectionQuoteGid): void
    {
        $quote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);

        $this->markQuoteToApplied($quote);

        $this->cancelAlternativeQuotes($quote);

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
        if (!$quote->isApplied()) {
            // todo
        }
        $quote->applied();
        $this->productQuoteRepository->save($quote);
    }

    private function cancelAlternativeQuotes(ProductQuote $quote): void
    {
        // todo
    }
}
