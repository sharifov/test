<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\BackOffice;
use frontend\helpers\JsonHelper;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\Cases;
use sales\exception\BoResponseException;
use sales\exception\ValidationException;
use sales\helpers\ErrorsToStringHelper;
use sales\services\cases\CasesSaleService;
use webapi\src\ApiCodeException;

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

    public function sendVoluntaryExchange(array $post): bool
    {
        return BackOffice::voluntaryExchange(self::mappingBORequest($post));
    }

    public static function mappingBORequest(array $post): array
    {
        $data['apiKey'] = $post['key'] ?? null;
        $data['bookingId'] = $post['bookingId'] ?? null;
        $data['billing'] = $post['billing'] ?? null;
        $data['payment'] = $post['payment_request'] ?? null;

        $data['exchange']['currency'] = $post['currency'] ?? null;
        $data['exchange']['validatingCarrier'] = $post['validatingCarrier'] ?? null;
        $data['exchange']['gds'] = $post['gds'] ?? null;
        $data['exchange']['pcc'] = $post['pcc'] ?? null;

        $data['exchange']['fareType'] = $post['fareType'] ?? null;
        $data['exchange']['cabin'] = $post['cabin'] ?? null;
        $data['exchange']['currencies'] = $post['currencies'] ?? null;
        $data['exchange']['currencyRates'] = $post['currencyRates'] ?? null;
        $data['exchange']['keys'] = $post['keys'] ?? null;
        $data['exchange']['meta'] = $post['meta'] ?? null;
        $data['exchange']['trips'] = $post['trips'] ?? null;
        $data['exchange']['passengers'] = $post['passengers'] ?? null;

        $data['exchange']['cons'] = $post['cons'] ?? null; /* TODO::  */
        $data['exchange']['tickets'] = $post['tickets'] ?? null; /* TODO::  */

        return $data;
    }
}
