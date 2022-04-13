<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use src\entities\cases\CaseEventLog;
use src\helpers\setting\SettingHelper;
use src\repositories\product\ProductQuoteRepository;
use src\services\TransactionManager;

/**
 * Class Confirm
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
 */
class Confirm
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CancelOtherReprotectionQuotes $cancelOtherReprotectionQuotes
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->cancelOtherReprotectionQuotes = $cancelOtherReprotectionQuotes;
    }

    public function handle(string $reprotectionQuoteGid, ?int $userId): void
    {
        $reprotectionQuote = $this->productQuoteRepository->findByGidFlightProductQuote($reprotectionQuoteGid);
        if (!$reprotectionQuote->flightQuote->isTypeReProtection()) {
            throw new \DomainException('Quote is not reprotection quote.');
        }
        if (!in_array($reprotectionQuote->pq_status_id, SettingHelper::getExchangeQuoteConfirmStatusList(), false)) {
            $processingList = ProductQuoteStatus::getNames(SettingHelper::getExchangeQuoteConfirmStatusList());
            throw new \DomainException('Quote not in confirmation statuses(' . implode(',', $processingList) . '). ' .
                'Current status(' . ProductQuoteStatus::getName($reprotectionQuote->pq_status_id) . ')');
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findParentRelated($reprotectionQuote, ProductQuoteChange::TYPE_RE_PROTECTION);

        if (!in_array($productQuoteChange->pqc_status_id, SettingHelper::getUpdatableInvoluntaryQuoteChange(), false)  || !$productQuoteChange->isTypeReProtection()) {
            $processingListAllowable = ProductQuoteStatus::getNames(SettingHelper::getUpdatableInvoluntaryQuoteChange());
            throw new \DomainException('Product Quote Change status is not in one of allowable statuses(' . implode(',', $processingListAllowable) . ') or is not Schedule Change. Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"; Current Type: "' . $productQuoteChange->getTypeName() . '"', 101);
        }

        $this->transactionManager->wrap(function () use ($reprotectionQuote, $productQuoteChange, $userId) {
            $this->confirmProductQuoteChange($productQuoteChange);
            $this->inProgressProductQuoteChange($productQuoteChange);
            $this->markQuoteToApplied($reprotectionQuote);
            $this->cancelOtherReprotectionQuotes->cancelByQuoteChange($productQuoteChange, $reprotectionQuote, $userId);
            $this->processingProductQuoteChange($productQuoteChange, $userId, $reprotectionQuote);
        });

        $this->createBoRequestJob($reprotectionQuote, $userId);
    }

    private function processingProductQuoteChange(ProductQuoteChange $change, ?int $userId, $reprotectionQuote): void
    {
        $change->customerDecisionConfirm($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::CONFIRM], [
                'productQuoteChangeId' => $change->pqc_id,
                'confirmedProductQuoteId' => $reprotectionQuote->pq_id,
                'originalProductQuoteId' => $reprotectionQuote->pqRelation->originProductQuote->pq_id,
            ]);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change): void
    {
        $fromStatus = $change->getSystemStatusName();
        $change->statusToComplete();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getSystemStatusName(),
            'decided' => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::CONFIRM]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function inProgressProductQuoteChange(ProductQuoteChange $change): void
    {
        $fromStatus = $change->getSystemStatusName();
        $change->inProgress();
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Product Quote Change updated status', [
            'gid' => $change->pqc_gid,
            'fromStatus' => $fromStatus,
            'toStatus' => $change->getSystemStatusName(),
            'decided' => ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::CONFIRM]
        ], CaseEventLog::CATEGORY_DEBUG);
    }

    private function createBoRequestJob(ProductQuote $quote, ?int $userId): void
    {
        $boJob = new BoRequestJob();
        $boJob->quoteGid = $quote->pq_gid;
        $boJob->userId = $userId;
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
}
