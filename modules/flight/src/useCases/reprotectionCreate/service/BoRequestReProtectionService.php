<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\components\BackOffice;
use common\models\CaseSale;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\exception\ValidationException;
use src\helpers\ErrorsToStringHelper;
use src\services\cases\CasesSaleService;
use Yii;
use yii\helpers\VarDumper;

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

    /**
     * @param string $bookingId
     * @param string $baseBookingId
     * @param Cases $case
     * @return array
     */
    public function getSaleByBookingId(string $bookingId, string $baseBookingId, Cases $case): array
    {
        if (!Yii::$app->params['settings']['enable_request_to_bo_sale']) {
            return [];
        }
        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
            'START: Request getSaleByBookingId, BaseBookingID: ' . $baseBookingId,
            ['fr_booking_id' => $bookingId, 'base_booking_id' => $baseBookingId]
        );
        try {
            $params = [
                'confirmation_number' => $baseBookingId,
            ];
            $response = BackOffice::sendRequest2('cs/search', $params, 'POST', 120);

            if ($response->isOk) {
                $result = $response->data;
                if (isset($result['items']) && is_array($result['items']) && count($result['items'])) {
                    foreach ($result['items'] as $item) {
                        if (
                            !empty($item['confirmationNumber']) && $item['confirmationNumber'] == $bookingId &&
                            !empty($item['saleStatus']) && in_array($item['saleStatus'], CaseSale::ALLOW_REPROTECTION_STATUS_LIST) &&
                            !empty($item['saleId'])
                        ) {
                            $case->addEventLog(
                                CaseEventLog::RE_PROTECTION_CREATE,
                                'START: Request DetailRequestToBackOffice SaleID: ' . $item['saleId'],
                                ['sale_id' => $item['saleId']]
                            );

                            $saleData = $this->casesSaleService->detailRequestToBackOffice($item['saleId'], 0, 120, 1);

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
                    }
                }
            } else {
                $responseStr = VarDumper::dumpAsString($response->content);
                throw new \RuntimeException('BO request Error: ' . $responseStr, -1);
            }
        } catch (\Throwable $throwable) {
            $message = VarDumper::dumpAsString([$throwable->getMessage(), $params], 20);
            Yii::error($message, 'BoRequestReProtectionService:getSaleByBookingId');
        }
        return [];
    }
}
