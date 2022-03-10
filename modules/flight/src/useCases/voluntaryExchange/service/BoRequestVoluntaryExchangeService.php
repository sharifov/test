<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\BackOffice;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\exception\ValidationException;
use src\helpers\ErrorsToStringHelper;
use src\services\cases\CasesSaleService;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;

/**
 * Class BoRequestReProtectionService
 *
 * @property CasesSaleService $casesSaleService
 * @property OrderCreateFromSaleForm $orderCreateFromSaleForm
 * @property OrderContactForm $orderContactForm
 */
class BoRequestVoluntaryExchangeService
{
    private CasesSaleService $casesSaleService;

    private OrderCreateFromSaleForm $orderCreateFromSaleForm;
    private OrderContactForm $orderContactForm;

    /**
     * @param CasesSaleService $casesSaleService
     */
    public function __construct(
        CasesSaleService $casesSaleService
    ) {
        $this->casesSaleService = $casesSaleService;
        $this->orderCreateFromSaleForm = new OrderCreateFromSaleForm();
        $this->orderContactForm = new OrderContactForm();
    }

    public function getSaleData(string $bookingId, Cases $case, int $type): ?array
    {
        $case->addEventLog(
            $type,
            'START: Request getSaleFrom BackOffice, BookingID: ' . $bookingId,
            ['fr_booking_id' => $bookingId]
        );
        $saleSearch = $this->casesSaleService->getSaleFromBo($bookingId);

        if (empty($saleSearch['saleId'])) {
            throw new BoResponseException('Sale not found by Booking ID(' . $bookingId . ') from "cs/search"');
        }
        $case->addEventLog(
            $type,
            'START: Request DetailRequestToBackOffice SaleID: ' . $saleSearch['saleId'],
            ['sale_id' => $saleSearch['saleId']]
        );
        $saleData = $this->casesSaleService->detailRequestToBackOffice($saleSearch['saleId'], 0, 120, 1);

        if (!$this->orderCreateFromSaleForm->load($saleData)) {
            throw new \DomainException('OrderCreateFromSaleForm not loaded');
        }
        if (!$this->orderCreateFromSaleForm->validate()) {
            throw new ValidationException(ErrorsToStringHelper::extractFromModel($this->orderCreateFromSaleForm));
        }
        $this->orderContactForm = OrderContactForm::fillForm($saleData);
        if (!$this->orderContactForm->validate()) {
            throw new ValidationException(ErrorsToStringHelper::extractFromModel($this->orderContactForm));
        }
        $case->addEventLog($type, 'Responses from BackOffice accepted successfully');

        return $saleData;
    }

    public function getOrderCreateFromSaleForm(): OrderCreateFromSaleForm
    {
        return $this->orderCreateFromSaleForm;
    }

    public function getOrderContactForm(): OrderContactForm
    {
        return $this->orderContactForm;
    }

    public function sendVoluntaryExchange(array $post, VoluntaryExchangeCreateForm $form): ?array
    {
        return BackOffice::voluntaryExchange(self::mappingBORequest($post, $form));
    }

    public static function mappingBORequest(array $post, VoluntaryExchangeCreateForm $form): array
    {
        $data = $post;
        if (array_key_exists('billing', $data)) {
            unset($data['billing']);
        }
        if (array_key_exists('payment_request', $data)) {
            unset($data['payment_request']);
        }

        $data['billing'] = self::fillBillingData($form->billingInfoForm);
        $data['payment'] = self::fillPaymentData($form->paymentRequestForm);

        return $data;
    }

    public function sendVoluntaryConfirm(array $requestData): ?array
    {
        return BackOffice::voluntaryExchange($requestData);
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
            $data = [
                'type' => mb_strtoupper($form->method_key),
            ];

            switch ($form->method_key) {
                case PaymentRequestForm::TYPE_METHOD_STRIPE:
                    $data['merchant'] = [
                        'tokenSource' => $form->stripeForm->token_source,
                    ];
                    break;
                default:
                    $data['card'] = [
                        'holderName' => $form->creditCardForm->holder_name,
                        'number' => $form->creditCardForm->number,
                        'expirationDate' => $form->creditCardForm->expiration_month . '/' . $form->creditCardForm->expiration_year,
                        'cvv' => $form->creditCardForm->cvv,
                    ];
                    break;
            }
        }

        return $data ?? null;
    }
}
