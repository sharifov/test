<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\repositories\product\ProductQuoteRepository;

/**
 * Class Modify
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 */
class Modify
{
    private ProductQuoteRepository $productQuoteRepository;
    private CasesRepository $casesRepository;

    public function __construct(ProductQuoteRepository $productQuoteRepository, CasesRepository $casesRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
    }

    public function handle(string $reprotectionQuoteGid, $newQuote): void
    {
        $reprotectionQuote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);

        try {
            $quote = $this->createNewReprotectionQuote($newQuote);
        } catch (\Throwable $e) {
            $this->processingCaseByError($newQuote);
            return;
        }

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
            ], 'ReprotectionDecision:Modify:BoRequestJob');
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

    private function createNewReprotectionQuote($quote): ProductQuote
    {
        // todo
        // create quote, if error thr exception
    }

    private function processingCaseByError(ProductQuote $quote): void
    {
        $case = $this->getCase($quote);
        $case->offIsAutomate();
        $case->error(null, 'Reprotection quote book error');
        $this->casesRepository->save($case);
    }

    private function getCase(ProductQuote $quote): Cases
    {
        // todo find case
    }
}
