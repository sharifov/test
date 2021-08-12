<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use modules\flight\models\FlightQuoteFlight;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class Refund
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property CasesRepository $casesRepository
 */
class Refund
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private CasesRepository $casesRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CasesRepository $casesRepository
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->casesRepository = $casesRepository;
    }

    public function handle(string $bookingId): void
    {
        $flightQuoteFLight = FlightQuoteFlight::find()->andWhere(['fqf_booking_id' => $bookingId])->one();
        if (!$flightQuoteFLight) {
            throw new \DomainException('Not found Flight Quote Flight with bookingId: ' . $bookingId);
        }
        $productQuote = $flightQuoteFLight->fqfFq->fqProductQuote ?? null;
        if (!$productQuote) {
            throw new \DomainException('Not found Product Quote with bookingId: ' . $bookingId);
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductQuoteId($productQuote->pq_id);
        if (!$productQuoteChange->isDecisionPending()) {
            throw new \DomainException('Product Quote Change status is invalid.');
        }

        try {
            $this->transactionManager->wrap(function () use ($productQuote, $productQuoteChange) {
                $this->refundProductQuoteChange($productQuoteChange);
                $this->cancelReprotectionQuotes($productQuote);
                $this->createRefunds();
            });
        } catch (\Throwable $e) {
            $case = $this->getCase($productQuoteChange);
            if ($case) {
                $this->processingCaseByError($case);
            }
            throw $e;
        }

        $this->createBoRequestJob($bookingId);
    }

    private function createRefunds()
    {
        $this->createOrderRefund();
        $this->createProductQuoteRefund();
    }

    private function createOrderRefund()
    {
        $refund = OrderRefund::createByScheduleChange();
        // todo
    }

    private function createProductQuoteRefund()
    {
        $refund = ProductQuoteRefund::createByScheduleChange();
        // todo
    }

    private function refundProductQuoteChange(ProductQuoteChange $change): void
    {
        $change->customerDecisionRefund(null, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]);
    }

    private function createBoRequestJob(string $bookingId): void
    {
        $boJob = new BoRequestJob();
        $boJob->bookingId = $bookingId;
        $jobId = \Yii::$app->queue_job->push($boJob);
        if (!$jobId) {
            \Yii::error([
                'message' => 'Reprotection decision BO Request Job not created',
                'bookingId' => $bookingId,
            ], 'ReprotectionDecision:Refund:BoRequestJob');
        }
    }

    public function cancelReprotectionQuotes(ProductQuote $quote): void
    {
        $quotes = ProductQuote::find()
            ->andWhere([
                'pq_id' => ProductQuoteRelation::find()
                    ->select(['pqr_related_pq_id'])
                    ->byParentQuoteId((int)$quote->pq_id)
                    ->reprotection()
            ])
            ->all();

        foreach ($quotes as $productQuote) {
            if (!$productQuote->isCanceled() && $productQuote->isFlight() && $productQuote->flightQuote->isTypeReProtection()) {
                $productQuote->cancelled(null, 'Canceled from reProtection');
                $this->productQuoteRepository->save($productQuote);
            }
        }
    }

    private function getCase(ProductQuoteChange $productQuoteChange): ?Cases
    {
        if ($productQuoteChange->pqc_case_id) {
            return $productQuoteChange->pqcCase;
        }
        return null;
    }

    private function processingCaseByError(Cases $case): void
    {
        $case->offIsAutomate();
        $case->error(null, 'Create refund error');
        $this->casesRepository->save($case);
    }
}
