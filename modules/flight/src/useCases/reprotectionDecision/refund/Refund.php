<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
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
 * @property OrderRefundRepository $orderRefundRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
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

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CasesRepository $casesRepository,
        OrderRefundRepository $orderRefundRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->casesRepository = $casesRepository;
        $this->orderRefundRepository = $orderRefundRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
    }

    public function handle(string $bookingId, ?int $userId): void
    {
        $flightQuoteFLight = FlightQuoteFlight::find()->andWhere(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one();
        if (!$flightQuoteFLight) {
            throw new \DomainException('Not found Flight Quote Flight with bookingId: ' . $bookingId);
        }
        $productQuote = $flightQuoteFLight->fqfFq->fqProductQuote ?? null;
        if (!$productQuote) {
            throw new \DomainException('Not found Product Quote with bookingId: ' . $bookingId);
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductQuoteId($productQuote->pq_id);
        if (!$productQuoteChange->isPending() || !$productQuoteChange->isTypeReProtection()) {
            throw new \DomainException('Product Quote Change status is not in "pending" or is not Schedule Change. Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"; Current Type: "' . $productQuoteChange->getTypeName() . '"', 101);
        }

        try {
            [$orderRefundId, $productQuoteRefundId] = $this->transactionManager->wrap(function () use ($productQuote, $productQuoteChange, $userId) {
                $this->confirmProductQuoteChange($productQuoteChange);
                $this->inProgressProductQuoteChange($productQuoteChange);
                $this->refundProductQuoteChange($productQuoteChange, $userId);
                $this->cancelReprotectionQuotesByProductQuoteChange($productQuoteChange, $userId);
                return $this->createRefunds($productQuote, $productQuoteChange);
            });
        } catch (\Throwable $e) {
            $case = $this->getCase($productQuoteChange);
            if ($case) {
                $this->processingCaseByError($case, $userId);
            }
            throw $e;
        }

        $this->createBoRequestJob($bookingId, $orderRefundId, $productQuoteRefundId, $userId);
    }

    private function createRefunds(ProductQuote $productQuote, ProductQuoteChange $productQuoteChange): array
    {
        $caseId = null;
        if ($case = $this->getCase($productQuoteChange)) {
            $caseId = $case->cs_id;
        }

        $orderRefundId = $this->createOrderRefund($productQuote, $caseId);
        $productQuoteRefundId = $this->createProductQuoteRefund($productQuote, $orderRefundId, $caseId);
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

    private function refundProductQuoteChange(ProductQuoteChange $change, ?int $userId): void
    {
        $change->customerDecisionRefund($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change): void
    {
        $fromStatus = $change->getClientStatusName();
        $change->statusToComplete();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getClientStatusName(),
            'decided' => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function inProgressProductQuoteChange(ProductQuoteChange $change): void
    {
        $fromStatus = $change->getClientStatusName();
        $change->inProgress();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getClientStatusName(),
            'decided'  => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::REFUND]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function createBoRequestJob(string $bookingId, int $orderRefundId, int $productQuoteRefundId, ?int $userId): void
    {
        $boJob = new BoRequestJob();
        $boJob->bookingId = $bookingId;
        $boJob->orderRefundId = $orderRefundId;
        $boJob->productQuoteRefundId = $productQuoteRefundId;
        $boJob->userId = $userId;
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

    private function getCase(ProductQuoteChange $productQuoteChange): ?Cases
    {
        if ($productQuoteChange->pqc_case_id) {
            return $productQuoteChange->pqcCase;
        }
        return null;
    }

    private function processingCaseByError(Cases $case, ?int $userId): void
    {
        $case->offIsAutomate();
        $case->error($userId, 'Create refund error');
        $this->casesRepository->save($case);
    }
}
