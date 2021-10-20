<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\BackOffice;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\Cases;
use sales\exception\BoResponseException;
use sales\exception\ValidationException;
use sales\helpers\ErrorsToStringHelper;
use sales\services\cases\CasesSaleService;

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

    public function sendVoluntaryExchange(ProductQuoteChange $productQuoteChange): bool
    {
        return BackOffice::voluntaryExchange($productQuoteChange);
    }
}
