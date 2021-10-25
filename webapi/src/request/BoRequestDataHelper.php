<?php

namespace webapi\src\request;

use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateForm;

class BoRequestDataHelper
{
    public static function getDataForVoluntaryCreateByForm(string $projectApikey, VoluntaryRefundCreateForm $form): array
    {
        $data = [
            'apiKey' => $projectApikey,
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
}
