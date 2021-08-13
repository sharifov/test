<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

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

    public function appliedQuote(string $quoteGid, ?int $userId): void
    {
        $quote = $this->productQuoteRepository->findByGidFlightProductQuote($quoteGid);
        if (!$quote->flightQuote->isTypeReProtection()) {
            throw new \DomainException('Quote is not reprotection quote.');
        }
        if (!$quote->isApplied()) {
            throw new \DomainException('Quote is not applied. ID: ' . $quote->pq_id);
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findParentRelated($quote);
        if (!$productQuoteChange->isCustomerDecisionModify()) {
            throw new \DomainException('Product Quote Change customer decision status is invalid.');
        }

        $responseBO = BackOffice::reprotectionCustomerDecisionModify(
            $this->getBookingId($productQuoteChange),
            $this->prepareQuoteToRequestData($quote)
        );

        if ($responseBO) {
            $this->transactionManager->wrap(function () use ($quote, $productQuoteChange, $userId) {
                $this->successProcessing($quote, $productQuoteChange, $userId);
            });
            return;
        }

        $this->transactionManager->wrap(function () use ($quote, $productQuoteChange, $userId) {
            $this->errorProcessing($quote, $productQuoteChange, $userId);
        });
    }

    private function successProcessing(ProductQuote $quote, ProductQuoteChange $productQuoteChange, ?int $userId): void
    {
        $this->markQuoteToInProgress($quote, $userId);
        $this->markQuoteChangeToInProgress($productQuoteChange);
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseBySuccessResult($case, $userId);
        }
    }

    private function errorProcessing(ProductQuote $quote, ProductQuoteChange $productQuoteChange, ?int $userId): void
    {
        $this->markQuoteToError($quote, $userId);
        $this->markQuoteChangeToError($productQuoteChange);
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseByErrorResult($case, $userId);
        }
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
        $case->error($userId, 'Reprotection quote book error');
        $this->casesRepository->save($case);
    }

    private function processingCaseBySuccessResult(Cases $case, ?int $userId): void
    {
        $case->awaiting($userId, 'Awaiting for reprotection quote status update');
        $this->casesRepository->save($case);
    }

    private function getCase(ProductQuoteChange $productQuoteChange): ?Cases
    {
        if ($productQuoteChange->pqc_case_id) {
            return $productQuoteChange->pqcCase;
        }
        return null;
    }

    private function markQuoteToError(ProductQuote $quote, ?int $userId): void
    {
        $quote->error($userId, 'Reprotection quote book error');
        $this->productQuoteRepository->save($quote);
    }

    private function markQuoteToInProgress(ProductQuote $quote, ?int $userId): void
    {
        $quote->inProgress($userId, 'Awaiting for reprotection quote status update');
        $this->productQuoteRepository->save($quote);
    }

    private function getBookingId(ProductQuoteChange $change): string
    {
        $lastFlightQuoteFlightBookingId = FlightQuoteFlight::find()->select(['fqf_booking_id'])->andWhere(['fqf_fq_id' => $change->pqcPq->flightQuote->fq_id])->orderBy(['fqf_id' => SORT_DESC])->scalar();
        if ($lastFlightQuoteFlightBookingId) {
            return $lastFlightQuoteFlightBookingId;
        }
        throw new \DomainException('Not found Booking Id. Quote ID: ' . $change->pqc_pq_id);
    }

    private function prepareQuoteToRequestData(ProductQuote $quote): array
    {
        return $quote->flightQuote->toArray(['gds', 'pcc', 'fareType', 'validatingCarrier', 'itineraryDump', 'trips']);
    }
}
