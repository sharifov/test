<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\flight\src\useCases\services\cases\CaseService;
use modules\flight\src\useCases\services\productQuote\ProductQuoteService;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChange\service\ProductQuoteChangeService;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\repositories\cases\CasesRepository;
use src\repositories\product\ProductQuoteRepository;
use src\services\TransactionManager;

/**
 * Class Refund
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property CasesRepository $casesRepository
 * @property OrderRefundRepository $orderRefundRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
 * @property CaseService $caseService
 * @property ProductQuoteService $productQuoteService
 */
class Refund
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private CasesRepository $casesRepository;
    private OrderRefundRepository $orderRefundRepository;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes;
    private CaseService $caseService;
    private ProductQuoteService $productQuoteService;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CasesRepository $casesRepository,
        OrderRefundRepository $orderRefundRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes,
        CaseService $caseService,
        ProductQuoteService $productQuoteService
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->casesRepository = $casesRepository;
        $this->orderRefundRepository = $orderRefundRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
        $this->caseService = $caseService;
        $this->productQuoteService = $productQuoteService;
    }

    public function handle(string $bookingId, ?int $userId): void
    {
        $productQuote = $this->productQuoteService->getProductQuote($bookingId);

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductIdAndType($productQuote->pq_id, ProductQuoteChange::TYPE_RE_PROTECTION);
        if (!$productQuoteChange->isPending() || !$productQuoteChange->isTypeReProtection()) {
            throw new \DomainException('Product Quote Change status is not in "pending" or is not Schedule Change. Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"; Current Type: "' . $productQuoteChange->getTypeName() . '"', 101);
        }

        if (ProductQuoteChangeService::notRefundableReProtection($productQuote->pq_id)) {
            throw new \DomainException('Product Quote Change not refundable.');
        }

        $case = $this->caseService->getCase($productQuoteChange, $productQuote);
        if (!$case) {
            \Yii::warning([
                'message' => 'Case not found by Product Quote Change (' . $productQuoteChange->pqc_id . ') or by Product Quote (' . $productQuote->pq_id . ')',
                'productQuoteId' => $productQuote->pq_id,
                'productQuoteGid' => $productQuote->pq_gid,
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
                'productQuoteChangeGid' => $productQuoteChange->pqc_gid,
            ], 'FlightController:reprotectionDecision:refund:DomainException');
            throw new \DomainException('Case not found by Product Quote Change (' . $productQuoteChange->pqc_id . ') or by Product Quote (' . $productQuote->pq_id . ')');
        }

        try {
            [$orderRefundId, $productQuoteRefundId] = $this->transactionManager->wrap(function () use ($productQuote, $productQuoteChange, $userId, $case) {
                $this->confirmProductQuoteChange($productQuoteChange, $case);
                $this->inProgressProductQuoteChange($productQuoteChange, $case);
                $this->refundProductQuoteChange($productQuoteChange, $userId, $case);
                $this->cancelReprotectionQuotesByProductQuoteChange($productQuoteChange, $userId);
                return $this->createRefunds($productQuote, $case);
            });
        } catch (\Throwable $e) {
            $this->processingCaseByError($case, $userId);
            throw $e;
        }

        $this->createBoRequestJob($bookingId, $orderRefundId, $productQuoteRefundId, $userId, $case->cs_id);
    }

    private function createRefunds(ProductQuote $productQuote, Cases $case): array
    {
        $orderRefundId = $this->createOrderRefund($productQuote, $case->cs_id);
        $productQuoteRefundId = $this->createProductQuoteRefund($productQuote, $orderRefundId, $case->cs_id);
        return [$orderRefundId, $productQuoteRefundId];
    }

    private function createOrderRefund(ProductQuote $productQuote, ?int $caseId): int
    {
        $orderRefund = OrderRefund::createByScheduleChange(
            OrderRefund::generateUid(),
            $productQuote->pq_order_id,
            $productQuote->pqOrder->or_app_total,
            $productQuote->pqOrder->or_client_currency,
            $productQuote->pqOrder->or_client_currency_rate,
            $productQuote->pqOrder->or_client_total,
            $caseId
        );
        $this->orderRefundRepository->save($orderRefund);
        return $orderRefund->orr_id;
    }

    private function createProductQuoteRefund(ProductQuote $productQuote, int $orderRefundId, ?int $caseId): int
    {
        $refund = ProductQuoteRefund::createByScheduleChange(
            $orderRefundId,
            $productQuote->pq_id,
            $productQuote->pq_price,
            $productQuote->pqOrder->or_client_currency,
            $productQuote->pqOrder->or_client_currency_rate,
            $caseId
        );
        $this->productQuoteRefundRepository->save($refund);
        return $refund->pqr_id;
    }

    private function refundProductQuoteChange(ProductQuoteChange $change, ?int $userId, Cases $case): void
    {
        $change->customerDecisionRefund($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($case->cs_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change, Cases $case): void
    {
        $fromStatus = $change->getSystemStatusName();
        $change->statusToComplete();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($case->cs_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getSystemStatusName(),
            'decided' => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function inProgressProductQuoteChange(ProductQuoteChange $change, Cases $case): void
    {
        $fromStatus = $change->getSystemStatusName();
        $change->inProgress();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($case->cs_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getSystemStatusName(),
            'decided'  => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function createBoRequestJob(string $bookingId, int $orderRefundId, int $productQuoteRefundId, ?int $userId, int $caseId): void
    {
        $boJob = new BoRequestJob();
        $boJob->bookingId = $bookingId;
        $boJob->orderRefundId = $orderRefundId;
        $boJob->productQuoteRefundId = $productQuoteRefundId;
        $boJob->userId = $userId;
        $boJob->caseId = $caseId;
        $jobId = \Yii::$app->queue_job->push($boJob);
        if (!$jobId) {
            \Yii::error([
                'message' => 'Reprotection decision BO Request Job not created',
                'bookingId' => $bookingId,
            ], 'ReprotectionDecision:Refund:BoRequestJob');
        }
    }

    public function cancelReprotectionQuotesByProductQuoteChange(ProductQuoteChange $productQuoteChange, ?int $userId): void
    {
        $quotes = ProductQuoteQuery::getProductQuotesByChangeQuote($productQuoteChange->pqc_id);

        foreach ($quotes as $productQuote) {
            if (!$productQuote->isCanceled() && $productQuote->isFlight() && $productQuote->flightQuote->isTypeReProtection()) {
                $productQuote->cancelled($userId, 'Canceled from reProtection');
                $this->productQuoteRepository->save($productQuote);
            }
        }
    }

    private function processingCaseByError(Cases $case, ?int $userId): void
    {
        $case->offIsAutomate();
        $case->error($userId, 'Create refund error');
        $this->casesRepository->save($case);
    }
}
