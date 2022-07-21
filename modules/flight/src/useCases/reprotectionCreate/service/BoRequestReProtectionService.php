<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\exception\ValidationException;
use src\helpers\ErrorsToStringHelper;
use src\services\cases\CasesSaleService;

/**
 * Class BoRequestReProtectionService
 *
 * @property CasesSaleService $casesSaleService
 * @property OrderCreateFromSaleForm $orderCreateFromSaleForm
 * @property OrderContactForm $orderContactForm
 */
class BoRequestReProtectionService
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

    public function getSaleData(string $bookingId, Cases $case)
    {
        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
            'START: Request getSaleFrom BackOffice, BookingID: ' . $bookingId,
            ['fr_booking_id' => $bookingId]
        );
        $projectKey = $case->project->api_key ?? null;
        $saleSearch = $this->casesSaleService->getSaleFromBo($bookingId, null, null, $projectKey);

        if (empty($saleSearch['saleId'])) {
            throw new BoResponseException('Sale not found by Booking ID(' . $bookingId . ') from "cs/search"');
        }
        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
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
        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Responses accepted successfully');

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
}
