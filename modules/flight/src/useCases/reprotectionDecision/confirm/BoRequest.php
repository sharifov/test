<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use common\components\BackOffice;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\repositories\product\ProductQuoteRepository;

/**
 * Class BoRequest
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 */
class BoRequest
{
    private ProductQuoteRepository $productQuoteRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        CasesRepository $casesRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
    }

    public function appliedQuote(string $quoteGid): void
    {
        $quote = $this->productQuoteRepository->findByGidFlightProductQuote($quoteGid);
        if (!$quote->isApplied()) {
            throw new \DomainException('Quote is not applied. ID: ' . $quote->pq_id);
        }

        $productQuoteChange = $this->productQuoteChangeRepository->findByProductQuoteId($quote->pq_id);
        if (!$productQuoteChange->isCustomerDecisionConfirm()) {
            throw new \DomainException('Product Quote Change customer decision status is invalid.');
        }

        $responseBO = BackOffice::reprotectionCustomerDecisionConfirm(
            $this->getBookingId($quote),
            $this->prepareQuoteToRequestData($quote)
        );

        if ($responseBO) {
            $this->successProcessing($quote, $productQuoteChange);
            return;
        }

        $this->errorProcessing($quote, $productQuoteChange);
    }

    private function successProcessing(ProductQuote $quote, ProductQuoteChange $productQuoteChange): void
    {
        $this->markQuoteToInProgress($quote);
        $this->markQuoteChangeToInProgress($productQuoteChange);
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseBySuccessResult($case);
        }
    }

    private function markQuoteChangeToInProgress(ProductQuoteChange $productQuoteChange): void
    {
        $productQuoteChange->inProgress();
        $this->productQuoteChangeRepository->save($productQuoteChange);
    }

    private function errorProcessing(ProductQuote $quote, ProductQuoteChange $productQuoteChange): void
    {
        $this->markQuoteToError($quote);
        // todo may be $productQuoteChange -> to error ?
        $case = $this->getCase($productQuoteChange);
        if ($case) {
            $this->processingCaseByErrorResult($case);
        }
    }

    private function processingCaseByErrorResult(Cases $case): void
    {
        $case->offIsAutomate();
        $case->error(null, 'Reprotection quote book error');
        $this->casesRepository->save($case);
    }

    private function processingCaseBySuccessResult(Cases $case): void
    {
        $case->awaiting(null, 'Awaiting for reprotection quote status update');
        $this->casesRepository->save($case);
    }

    private function getCase(ProductQuoteChange $productQuoteChange): ?Cases
    {
        if ($productQuoteChange->pqc_case_id) {
            return $productQuoteChange->pqcCase;
        }
        return null;
    }

    private function markQuoteToError(ProductQuote $quote): void
    {
        $quote->error(null, 'Reprotection quote book error');
        $this->productQuoteRepository->save($quote);
    }

    private function markQuoteToInProgress(ProductQuote $quote): void
    {
        $quote->inProgress(null, 'Awaiting for reprotection quote status update');
        $this->productQuoteRepository->save($quote);
    }

    private function getBookingId(ProductQuote $quote): string
    {
        $lastFlightQuoteFlightBookingId = FlightQuoteFlight::find()->select(['fqf_booking_id'])->andWhere(['fqf_fq_id' => $quote->flightQuote->fq_id])->orderBy(['fqf_id' => SORT_DESC])->scalar();
        if ($lastFlightQuoteFlightBookingId) {
            return $lastFlightQuoteFlightBookingId;
        }
        throw new \DomainException('Not found Booking Id. Quote ID: ' . $quote->pq_id);
    }

    private function prepareQuoteToRequestData(ProductQuote $quote): array
    {
        return $quote->flightQuote->toArray(['trips']);
    }
}
