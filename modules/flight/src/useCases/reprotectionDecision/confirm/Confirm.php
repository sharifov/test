<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use modules\flight\src\useCases\reprotectionDecision\CancelOtherReprotectionQuotes;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use sales\entities\cases\CaseEventLog;
use sales\helpers\setting\SettingHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

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

        $productQuoteChange = $this->productQuoteChangeRepository->findParentRelated($reprotectionQuote);
        if (!$productQuoteChange->isPending()) {
            throw new \DomainException('Product Quote Change status is not in "Decision pending". Current status "' . ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . '"', 101);
        }

        $this->transactionManager->wrap(function () use ($reprotectionQuote, $productQuoteChange, $userId) {
            $this->markQuoteToApplied($reprotectionQuote);
            $this->cancelOtherReprotectionQuotes->cancel($reprotectionQuote, $userId);
            $this->confirmProductQuoteChange($productQuoteChange, $userId);
        });

        $this->createBoRequestJob($reprotectionQuote, $userId);
    }

    private function confirmProductQuoteChange(ProductQuoteChange $change, ?int $userId): void
    {
        $change->customerDecisionConfirm($userId, new \DateTimeImmutable());
        $this->productQuoteChangeRepository->save($change);
        CaseEventLog::add($change->pqc_case_id, CaseEventLog::REPROTECTION_DECISION, 'Flight reprotection decided: ' . ProductQuoteChangeDecisionType::LIST[ProductQuoteChangeDecisionType::CONFIRM]);
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
