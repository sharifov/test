<?php

namespace modules\flight\src\useCases\reprotectionDecision;

interface CustomerDecisionService
{
    public function reprotectionCustomerDecisionConfirm(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool;
    public function reprotectionCustomerDecisionModify(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool;
    public function reprotectionCustomerDecisionRefund(int $projectId, string $bookingId): bool;
}
