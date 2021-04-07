<?php

namespace modules\flight\src\services\api;

use modules\flight\src\forms\api\FlightUpdateRequestApiForm;
use modules\flight\src\forms\api\PaymentApiForm;
use modules\flight\src\forms\api\TicketIssueFlightApiForm;
use sales\helpers\ErrorsToStringHelper;

/**
 * Class TicketIssueCheckDataService
 */
class TicketIssueCheckDataService
{
    public static function checkFlights(array $flights): bool
    {
        foreach ($flights as $key => $flight) {
            $flightApiForm = new TicketIssueFlightApiForm();

            if (!$flightApiForm->load($flight)) {
                throw new \DomainException('FlightApiForm is not loaded');
            }
            if (!$flightApiForm->validate()) {
                throw new \DomainException(ErrorsToStringHelper::extractFromModel($flightApiForm));
            }
        }
        return true;
    }

    public static function checkPayments(array $payments): bool
    {
        foreach ($payments as $key => $payment) {
            $ticketIssuePaymentApiForm = new PaymentApiForm();

            if (!$ticketIssuePaymentApiForm->load($payment)) {
                throw new \DomainException('PaymentApiForm is not loaded');
            }
            if (!$ticketIssuePaymentApiForm->validate()) {
                throw new \DomainException(ErrorsToStringHelper::extractFromModel($ticketIssuePaymentApiForm));
            }
        }
        return true;
    }
}
