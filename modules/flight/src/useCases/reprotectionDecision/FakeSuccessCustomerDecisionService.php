<?php

namespace modules\flight\src\useCases\reprotectionDecision;

class FakeSuccessCustomerDecisionService implements CustomerDecisionService
{
    public function reprotectionCustomerDecisionConfirm(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool
    {
        return true;
    }

    public function reprotectionCustomerDecisionModify(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool
    {
        return true;
    }

    public function reprotectionCustomerDecisionRefund(int $projectId, string $bookingId): bool
    {
        return true;
    }
}
