<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use common\components\BackOffice;
use modules\flight\models\FlightQuoteFlight;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
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
 * @property OrderRefundRepository $orderRefundRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 */
class BoRequest
{
    private ProductQuoteRepository $productQuoteRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private TransactionManager $transactionManager;
    private OrderRefundRepository $orderRefundRepository;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        CasesRepository $casesRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        TransactionManager $transactionManager,
        OrderRefundRepository $orderRefundRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->transactionManager = $transactionManager;
        $this->orderRefundRepository = $orderRefundRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
    }

    public function refund(string $bookingId, int $orderRefundId, int $productQuoteRefundId, ?int $userId): void
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
            $this->transactionManager->wrap(function () use ($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId) {
                $this->successProcessing($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId);
            });
            return;
        }

        $this->transactionManager->wrap(function () use ($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId) {
            $this->errorProcessing($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId);
        });
    }

    private function successProcessing(ProductQuoteChange $productQuoteChange, int $orderRefundId, int $productQuoteRefundId, ?int $userId): void
    {
        $this->markQuoteChangeToInProgress($productQuoteChange);
        $this->markRefundsToProcessing($orderRefundId, $productQuoteRefundId);
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseBySuccessResult($case, $userId);
        }
    }

    private function errorProcessing(ProductQuoteChange $productQuoteChange, int $orderRefundId, int $productQuoteRefundId, ?int $userId): void
    {
        $this->markQuoteChangeToError($productQuoteChange);
        $this->markRefundsToError($orderRefundId, $productQuoteRefundId);
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseByErrorResult($case, $userId);
        }
    }

    private function markRefundsToError(int $orderRefundId, int $productQuoteRefundId): void
    {
        $this->markOrderRefundToError($orderRefundId);
        $this->markProductQuoteRefundToError($productQuoteRefundId);
    }

    private function markOrderRefundToError(int $orderRefundId): void
    {
        $refund = $this->orderRefundRepository->find($orderRefundId);
        $refund->error();
        $this->orderRefundRepository->save($refund);
    }

    private function markProductQuoteRefundToError(int $productQuoteRefundId): void
    {
        $refund = $this->productQuoteRefundRepository->find($productQuoteRefundId);
        $refund->error();
        $this->productQuoteRefundRepository->save($refund);
    }

    private function markRefundsToProcessing(int $orderRefundId, int $productQuoteRefundId): void
    {
        $this->markOrderRefundToProcessing($orderRefundId);
        $this->markProductQuoteRefundToProcessing($productQuoteRefundId);
    }

    private function markOrderRefundToProcessing(int $orderRefundId): void
    {
        $refund = $this->orderRefundRepository->find($orderRefundId);
        $refund->processing();
        $this->orderRefundRepository->save($refund);
    }

    private function markProductQuoteRefundToProcessing(int $productQuoteRefundId): void
    {
        $refund = $this->productQuoteRefundRepository->find($productQuoteRefundId);
        $refund->processing();
        $this->productQuoteRefundRepository->save($refund);
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

    private function processingCaseByErrorResult(Cases $case, ?int $userId): void
    {
        $case->offIsAutomate();
        $case->error($userId, 'Refund request error');
        $this->casesRepository->save($case);
    }

    private function processingCaseBySuccessResult(Cases $case, ?int $userId): void
    {
        $case->awaiting($userId, 'Awaiting for Refund request update');
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
