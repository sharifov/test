<?php

namespace webapi\src\request;

use modules\featureFlag\FFlag;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\flight\src\useCases\api\voluntaryRefundConfirm\VoluntaryRefundConfirmForm;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use src\services\CurrencyHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use Yii;

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
                'refundable' => $ticket->refundable
            ];
        }
        $data['billing'] = BoRequestDataHelper::fillBillingData($form->billingInfoForm);
        $data['payment'] = BoRequestDataHelper::fillPaymentData($form->paymentRequestForm);
        if ($form->paymentRequestForm) {
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
        $data['billing'] = BoRequestDataHelper::fillBillingData($form->billingInfoForm);
        $data['payment'] = BoRequestDataHelper::fillPaymentData($form->paymentRequestForm);
        if ($form->paymentRequestForm) {
            $data['refund']['refundCost'] = $form->paymentRequestForm->amount;
        }

        /** @fflag FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS, Send additional info to BO endpoints enable\disable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS)) {
            $service = RequestBoAdditionalSources::getServiceByType(RequestBoAdditionalSources::TYPE_PRODUCT_QUOTE_REFUND);
            if ($service) {
                $data['additionalInfo'] = $service->prepareAdditionalInfo($productQuoteRefund);
            } else {
                \Yii::error([
                    'message' => 'Service not found by type: ' . RequestBoAdditionalSources::getTypeNameById(RequestBoAdditionalSources::TYPE_PRODUCT_QUOTE_REFUND),
                ], 'BoRequestDataHelper:getDataForVoluntaryRefundConfirm:additionalInfo');
            }
        }
        return $data;
    }

    /**
     * @param string $apiKey
     * @param string $bookingId
     * @param FlightQuoteTicket[] $flightQuoteTickets
     * @return array
     */
    public static function getRequestDataForVoluntaryRefundData(string $apiKey, string $bookingId, array $flightQuoteTickets = []): array
    {
        $data = [
            'apiKey' => $apiKey,
            'bookingId' => $bookingId,
            'tickets' => []
        ];

        if (!empty($flightQuoteTickets)) {
            foreach ($flightQuoteTickets as $flightQuoteTicket) {
                $data['tickets'][] = $flightQuoteTicket->fqt_ticket_number;
            }
        }
        return $data;
    }

    public static function fillBillingData(?BillingInfoForm $form): ?array
    {
        if ($form) {
            $data = [
                'address' => $form->address_line1,
                'countryCode' => $form->country_id,
                'country' => $form->country,
                'city' => $form->city,
                'state' => $form->state,
                'zip' => $form->zip,
                'phone' => $form->contact_phone,
                'email' => $form->contact_email
            ];
        }
        return $data ?? null;
    }

    /**
     * @param PaymentRequestForm|null $form
     * @return array|null
     */
    public static function fillPaymentData(?PaymentRequestForm $form): ?array
    {
        if ($form) {
            switch ($form->method_key) {
                case PaymentRequestForm::TYPE_METHOD_STRIPE:
                    $data = [
                        'type' => mb_strtoupper($form->method_key),
                        'merchant' => [
                            'tokenSource' => $form->stripeForm->token_source,
                        ],
                    ];
                    break;
                case PaymentRequestForm::TYPE_METHOD_CARD:
                    $data = [
                        'type' => mb_strtoupper($form->method_key),
                        'card' => [
                            'holderName' => $form->creditCardForm->holder_name,
                            'number' => $form->creditCardForm->number,
                            'expirationDate' => $form->creditCardForm->expiration_month . '/' . $form->creditCardForm->expiration_year,
                            'cvv' => $form->creditCardForm->cvv,
                        ],
                    ];
                    break;
                default:
                    break;
            }
        }

        return $data ?? null;
    }

    public static function prepareAdditionalInfoToBoRequest($quote): array
    {
        $createdBy = null;
        $createdAt = null;
        $expireAt = null;

        if ($quote instanceof ProductQuote) {
            $createdBy = $quote->getPqCreatedUser()->limit(1)->one();
            $createdAt = $quote->pq_created_dt ?? null;
            $expireAt = $quote->pq_expiration_dt ?? null;
        }
        if ($quote instanceof ProductQuoteRefund) {
            $createdBy = $quote->getCreatedUser()->limit(1)->one();
            $createdAt = $quote->pqr_created_dt ?? null;
            $expireAt = $quote->pqr_expiration_dt ?? null;
        }

        return [
            'user' => [
                'name' => $createdBy->full_name ?? null,
                'email' => $createdBy->email ?? null,
            ],
            'quote' => [
                'created' => $createdAt,
                'expire' => $expireAt,
            ],
        ];
    }
}
