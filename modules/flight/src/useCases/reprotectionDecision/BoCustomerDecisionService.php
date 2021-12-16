<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use common\components\BackOffice;

class BoCustomerDecisionService implements CustomerDecisionService
{
    public function reprotectionCustomerDecisionConfirm(
        int $projectId,
        string $bookingId,
        array $quote,
        string $reprotectionQuoteGid
    ): bool {
        return BackOffice::reprotectionCustomerDecisionConfirm($projectId, $bookingId, $quote, $reprotectionQuoteGid);
    }

    public function reprotectionCustomerDecisionModify(
        int $projectId,
        string $bookingId,
        array $quote,
        string $reprotectionQuoteGid
    ): bool {
        return BackOffice::reprotectionCustomerDecisionModify($projectId, $bookingId, $quote, $reprotectionQuoteGid);
    }

    public function reprotectionCustomerDecisionRefund(int $projectId, string $bookingId): bool
    {
        return BackOffice::reprotectionCustomerDecisionRefund($projectId, $bookingId);
    }
}
