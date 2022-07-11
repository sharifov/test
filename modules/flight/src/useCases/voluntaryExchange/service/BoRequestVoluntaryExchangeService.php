<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\BackOffice;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\exception\ValidationException;
use src\helpers\ErrorsToStringHelper;
use src\services\cases\CasesSaleService;
use webapi\src\request\BoRequestDataHelper;
use webapi\src\request\RequestBoAdditionalSources;
use Yii;

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

        $data['billing'] = BoRequestDataHelper::fillBillingData($form->billingInfoForm);
        $data['payment'] = BoRequestDataHelper::fillPaymentData($form->paymentRequestForm);

        $productQuote = ProductQuoteQuery::getProductQuoteByBookingId($form->bookingId);
        if ($productQuote) {
            $service = RequestBoAdditionalSources::getServiceByType(RequestBoAdditionalSources::TYPE_PRODUCT_QUOTE);
            if ($service) {
                $data['additionalInfo'] = $service->prepareAdditionalInfo($productQuote);
            } else {
                \Yii::error([
                    'message' => 'Service not found by type: ' . RequestBoAdditionalSources::getTypeNameById(RequestBoAdditionalSources::TYPE_PRODUCT_QUOTE),
                ], 'BoRequestVoluntaryExchangeService:mappingBORequest:additionalInfo');
            }
        } else {
            \Yii::error([
                'message' => 'Not found product quote by booking ID: ' . $form->bookingId,
            ], 'BoRequestVoluntaryExchangeService:mappingBORequest:additionalInfo');
        }

        return $data;
    }

    public function sendVoluntaryConfirm(array $requestData): ?array
    {
        return BackOffice::voluntaryExchange($requestData);
    }
}
