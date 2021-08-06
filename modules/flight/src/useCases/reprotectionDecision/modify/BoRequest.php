<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

use common\components\BackOffice;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\repositories\product\ProductQuoteRepository;

/**
 * Class BoRequest
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 */
class BoRequest
{
    private ProductQuoteRepository $productQuoteRepository;
    private CasesRepository $casesRepository;

    public function __construct(ProductQuoteRepository $productQuoteRepository, CasesRepository $casesRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
    }

    public function appliedQuote(string $quoteGid): void
    {
        $quote = $this->productQuoteRepository->findByGidFlightProductQuote($quoteGid);

        $data = $this->prepareQuoteToRequestData($quote);

        $bookingId = $this->getBookingId($quote);

        $responseBO = BackOffice::reprotectionCustomerDecisionModify($bookingId, $data);

        if ($responseBO) {
            $this->successProcessing($quote);
            return;
        }

        $this->errorProcessing($quote);
    }

    private function successProcessing(ProductQuote $quote): void
    {
        $this->markQuoteToInProgress($quote);
        $this->processingCaseBySuccessResult($quote);
    }

    private function errorProcessing(ProductQuote $quote): void
    {
        $this->markQuoteToError($quote);
        $this->processingCaseByErrorResult($quote);
    }

    private function processingCaseByErrorResult(ProductQuote $quote): void
    {
        $case = $this->getCase($quote);
        $case->offIsAutomate();
        $case->error(null, 'Reprotection quote book error');
        $this->casesRepository->save($case);
    }

    private function processingCaseBySuccessResult(ProductQuote $quote): void
    {
        $case = $this->getCase($quote);
        $case->awaiting(null, 'Awaiting for reprotection quote status update');
        $this->casesRepository->save($case);
    }

    private function getCase(ProductQuote $quote): Cases
    {
        // todo find case
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
        // todo
        return '';
    }

    private function prepareQuoteToRequestData(ProductQuote $quote): array
    {
        // todo
        return [];
    }
}
