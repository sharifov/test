<?php

namespace webapi\src\request;

use modules\flight\models\FlightQuoteTicket;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\flight\src\useCases\api\voluntaryRefundConfirm\VoluntaryRefundConfirmForm;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateForm;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use sales\services\CurrencyHelper;

class BoRequestDataHelper
{
    public static function getDataForVoluntaryCreateByForm(string $projectApiKey, VoluntaryRefundCreateForm $form): array
    {
        $data = [
            'apiKey' => $projectApiKey,
            'bookingId' => $form->bookingId,
        ];
        $data['refund'] = [
            'orderId' => $form->refundForm->orderId,
            'currency' => $form->refundForm->currency
        ];
        foreach ($form->refundForm->ticketForms as $ticket) {
            $data['refund']['tickets'][] = [
                'number' => $ticket->number,
                'airlinePenalty' => $ticket->airlinePenalty,
                'processingFee' => $ticket->processingFee,
                'refundable' => $ticket->refundAmount
            ];
        }
        if ($form->billingInfoForm) {
            $data['billing'] = [
                'address' => $form->billingInfoForm->address_line1,
                'countryCode' => $form->billingInfoForm->country_id,
                'country' => $form->billingInfoForm->country,
                'city' => $form->billingInfoForm->city,
                'state' => $form->billingInfoForm->state,
                'zip' => $form->billingInfoForm->zip,
                'phone' => $form->billingInfoForm->contact_phone,
                'email' => $form->billingInfoForm->contact_email
            ];
        }
        if ($form->paymentRequestForm) {
            $data['payment'] = [
                'type' => mb_strtoupper($form->paymentRequestForm->method_key),
                'card' => [
                    'holderName' => $form->paymentRequestForm->creditCardForm->holder_name,
                    'number' => $form->paymentRequestForm->creditCardForm->number,
                    'expirationDate' => $form->paymentRequestForm->creditCardForm->expiration_month . '/' . $form->paymentRequestForm->creditCardForm->expiration_year,
                    'cvv' => $form->paymentRequestForm->creditCardForm->cvv
                ]
            ];
            $data['refund']['refundCost'] = $form->paymentRequestForm->amount;
        }
        return $data;
    }

    public static function getDataForVoluntaryRefundConfirm(string $projectApiKey, VoluntaryRefundConfirmForm $form, ProductQuoteRefund $productQuoteRefund): array
    {
        $data = [
            'apiKey' => $projectApiKey,
            'bookingId' => $form->bookingId,
        ];
        $data['refund'] = [
            'orderId' => $form->orderId,
            'currency' => $productQuoteRefund->clientCurrency->cur_code
        ];

        foreach ($productQuoteRefund->productQuoteObjectRefunds as $productQuoteObjectRefund) {
            $data['refund']['tickets'][] = [
                'number' => FlightQuoteTicketRefund::findOne(['fqtr_id' => $productQuoteObjectRefund->pqor_quote_object_id])->fqtr_ticket_number ?? null,
                'airlinePenalty' => CurrencyHelper::convertFromBaseCurrency($productQuoteObjectRefund->pqor_penalty_amount, $productQuoteObjectRefund->pqor_client_currency_rate),
                'processingFee' => CurrencyHelper::convertFromBaseCurrency($productQuoteObjectRefund->pqor_processing_fee_amount, $productQuoteObjectRefund->pqor_client_currency_rate),
                'refundable' => $productQuoteObjectRefund->pqor_client_refund_amount
            ];
        }
        if ($form->billingInfoForm) {
            $data['billing'] = [
                'address' => $form->billingInfoForm->address_line1,
                'countryCode' => $form->billingInfoForm->country_id,
                'country' => $form->billingInfoForm->country,
                'city' => $form->billingInfoForm->city,
                'state' => $form->billingInfoForm->state,
                'zip' => $form->billingInfoForm->zip,
                'phone' => $form->billingInfoForm->contact_phone,
                'email' => $form->billingInfoForm->contact_email
            ];
        }
        if ($form->paymentRequestForm) {
            $data['payment'] = [
                'type' => $form->paymentRequestForm->method_key,
                'card' => [
                    'holderName' => $form->paymentRequestForm->creditCardForm->holder_name,
                    'number' => $form->paymentRequestForm->creditCardForm->number,
                    'expirationDate' => $form->paymentRequestForm->creditCardForm->expiration_month . '/' . $form->paymentRequestForm->creditCardForm->expiration_year,
                    'cvv' => $form->paymentRequestForm->creditCardForm->cvv
                ]
            ];
            $data['refund']['refundCost'] = $form->paymentRequestForm->amount;
        }
        return $data;
    }

    /**
     * @param string $apiKey
     * @param string $bookingId
     * @param FlightQuoteTicket[] $flightQuoteTickets
     * @return array
     */
    public static function getRequestDataForVoluntaryRefundData(string $apiKey, string $bookingId, array $flightQuoteTickets): array
    {
        $data = [
            'apiKey' => $apiKey,
            'bookingId' => $bookingId,
            'tickets' => []
        ];

        if ($flightQuoteTickets) {
            foreach ($flightQuoteTickets as $flightQuoteTicket) {
                $data['tickets'][] = $flightQuoteTicket->fqt_ticket_number;
            }
        }
        return $data;
    }
}
