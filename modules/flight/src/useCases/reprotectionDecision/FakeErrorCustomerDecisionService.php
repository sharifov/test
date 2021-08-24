<?php

namespace modules\flight\src\useCases\reprotectionDecision;

class FakeErrorCustomerDecisionService implements CustomerDecisionService
{
    public function reprotectionCustomerDecisionConfirm(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool
    {
        return false;
    }

    public function reprotectionCustomerDecisionModify(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool
    {
        return false;
    }

    public function reprotectionCustomerDecisionRefund(int $projectId, string $bookingId): bool
    {
        return false;
    }
}
