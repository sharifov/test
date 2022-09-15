<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\useCases\reprotectionDecision\CustomerDecisionService;
use modules\flight\src\useCases\services\productQuote\ProductQuoteService;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use src\entities\cases\Cases;
use src\repositories\cases\CasesRepository;
use src\repositories\product\ProductQuoteRepository;
use src\services\TransactionManager;

/**
 * Class BoRequest
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property TransactionManager $transactionManager
 * @property OrderRefundRepository $orderRefundRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property CustomerDecisionService $customerDecisionService
 * @property ProductQuoteService $productQuoteService
 */
class BoRequest
{
    private ProductQuoteRepository $productQuoteRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private TransactionManager $transactionManager;
    private OrderRefundRepository $orderRefundRepository;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private CustomerDecisionService $customerDecisionService;
    private ProductQuoteService $productQuoteService;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        CasesRepository $casesRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        TransactionManager $transactionManager,
        OrderRefundRepository $orderRefundRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        CustomerDecisionService $customerDecisionService,
        ProductQuoteService $productQuoteService
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->transactionManager = $transactionManager;
        $this->orderRefundRepository = $orderRefundRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->customerDecisionService = $customerDecisionService;
        $this->productQuoteService = $productQuoteService;
    }

    public function refund(string $bookingId, int $orderRefundId, int $productQuoteRefundId, ?int $userId, int $caseId): void
    {
        $originalProductQuote = $this->productQuoteService->getProductQuote($bookingId);

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductIdAndType($originalProductQuote->pq_id, ProductQuoteChange::TYPE_RE_PROTECTION);
        if (!$productQuoteChange->isCustomerDecisionRefund()) {
            throw new \DomainException('Product Quote Change status is invalid.');
        }

        $responseBO = $this->customerDecisionService->reprotectionCustomerDecisionRefund(
            $originalProductQuote->pqProduct->pr_project_id,
            $this->getBookingId($originalProductQuote)
        );

        if ($responseBO) {
            $this->transactionManager->wrap(function () use ($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId, $caseId) {
                $this->successProcessing($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId, $caseId);
            });
            return;
        }

        $this->transactionManager->wrap(function () use ($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId, $caseId) {
            $this->errorProcessing($productQuoteChange, $orderRefundId, $productQuoteRefundId, $userId, $caseId);
        });
    }

    private function successProcessing(ProductQuoteChange $productQuoteChange, int $orderRefundId, int $productQuoteRefundId, ?int $userId, int $caseId): void
    {
        $this->markQuoteChangeToCancel($productQuoteChange);
        $this->markRefundsToProcessing($orderRefundId, $productQuoteRefundId);
        $case = $this->getCase($caseId);
        $this->processingCaseBySuccessResult($case, $userId);
    }

    private function errorProcessing(ProductQuoteChange $productQuoteChange, int $orderRefundId, int $productQuoteRefundId, ?int $userId, int $caseId): void
    {
        $this->markQuoteChangeToError($productQuoteChange);
        $this->markRefundsToError($orderRefundId, $productQuoteRefundId);
        $case = $this->getCase($caseId);
        $this->processingCaseByErrorResult($case, $userId);
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

    private function markQuoteChangeToCancel(ProductQuoteChange $productQuoteChange): void
    {
        $productQuoteChange->cancel();
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

    private function getCase(int $caseId): Cases
    {
        return $this->casesRepository->find($caseId);
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
