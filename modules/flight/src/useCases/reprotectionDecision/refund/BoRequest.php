<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use common\components\BackOffice;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class BoRequest
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property TransactionManager $transactionManager
 */
class BoRequest
{
    private ProductQuoteRepository $productQuoteRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private TransactionManager $transactionManager;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        CasesRepository $casesRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        TransactionManager $transactionManager
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->transactionManager = $transactionManager;
    }

    public function refund(string $bookingId): void
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
        if (!$productQuoteChange->isCustomerDecisionRefund()) {
            throw new \DomainException('Product Quote Change status is invalid.');
        }

        $responseBO = BackOffice::reprotectionCustomerDecisionRefund(
            $this->getBookingId($productQuote)
        );

        if ($responseBO) {
            $this->transactionManager->wrap(function () use ($productQuoteChange) {
                $this->successProcessing($productQuoteChange);
            });
            return;
        }

        $this->transactionManager->wrap(function () use ($productQuoteChange) {
            $this->errorProcessing($productQuoteChange);
        });
    }

    private function successProcessing(ProductQuoteChange $productQuoteChange): void
    {
        $this->markQuoteChangeToInProgress($productQuoteChange);
        $this->markRefundsToInProgress();
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseBySuccessResult($case);
        }
    }

    private function errorProcessing(ProductQuoteChange $productQuoteChange): void
    {
        $this->markQuoteChangeToError($productQuoteChange);
        $this->markRefundsToError();
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseByErrorResult($case);
        }
    }

    private function markRefundsToError(): void
    {
        $this->markOrderRefundToError();
        $this->markProductQuoteRefundToError();
    }

    private function markOrderRefundToError(): void
    {
        // todo
    }

    private function markProductQuoteRefundToError(): void
    {
        // todo
    }

    private function markRefundsToInProgress(): void
    {
        $this->markOrderRefundToInProgress();
        $this->markProductQuoteRefundToInProgress();
    }

    private function markOrderRefundToInProgress(): void
    {
        // todo
    }

    private function markProductQuoteRefundToInProgress(): void
    {
        // todo
    }

    private function markQuoteChangeToInProgress(ProductQuoteChange $productQuoteChange): void
    {
        $productQuoteChange->inProgress();
        $this->productQuoteChangeRepository->save($productQuoteChange);
    }

    private function markQuoteChangeToError(ProductQuoteChange $productQuoteChange): void
    {
        $productQuoteChange->error();
        $this->productQuoteChangeRepository->save($productQuoteChange);
    }

    private function processingCaseByErrorResult(Cases $case): void
    {
        $case->offIsAutomate();
        $case->error(null, 'Refund request error');
        $this->casesRepository->save($case);
    }

    private function processingCaseBySuccessResult(Cases $case): void
    {
        $case->awaiting(null, 'Awaiting for Refund request update');
        $this->casesRepository->save($case);
    }

    private function getCase(ProductQuoteChange $productQuoteChange): ?Cases
    {
        if ($productQuoteChange->pqc_case_id) {
            return $productQuoteChange->pqcCase;
        }
        return null;
    }

    private function getBookingId(ProductQuote $quote): string
    {
        $lastFlightQuoteFlightBookingId = FlightQuoteFlight::find()->select(['fqf_booking_id'])->andWhere(['fqf_fq_id' => $quote->flightQuote->fq_id])->orderBy(['fqf_id' => SORT_DESC])->scalar();
        if ($lastFlightQuoteFlightBookingId) {
            return $lastFlightQuoteFlightBookingId;
        }
        throw new \DomainException('Not found Booking Id. Quote ID: ' . $quote->pq_id);
    }
}
