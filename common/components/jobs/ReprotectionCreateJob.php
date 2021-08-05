<?php

namespace common\components\jobs;

use common\components\HybridService;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reprotectionCreate\service\ReprotectionCreateService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use sales\exception\BoResponseException;
use sales\exception\CheckRestrictionException;
use sales\exception\ValidationException;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\services\cases\CasesSaleService;
use Yii;
use yii\queue\JobInterface;
use yii\queue\Queue;
use sales\repositories\product\ProductQuoteRepository;

/**
 * @property int $flight_request_id
 */
class ReprotectionCreateJob extends BaseJob implements JobInterface
{
    public $flight_request_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): void
    {
        $this->executionTimeRegister();

        $reProtectionCreateService = Yii::createObject(ReprotectionCreateService::class);
        $casesSaleService = Yii::createObject(CasesSaleService::class);
        $flightQuoteManageService = Yii::createObject(FlightQuoteManageService::class);
        $productQuoteRelationRepository = Yii::createObject(ProductQuoteRelationRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found by (' . $this->flight_request_id . ')');
            }

            $case = $reProtectionCreateService->createCase($flightRequest->fr_booking_id);

            if ($order = $reProtectionCreateService->getOrderByBookingId($flightRequest->fr_booking_id)) {
                if (!$oldProductQuote = $reProtectionCreateService->declineOldProductQuote($order)) {
                    $reProtectionCreateService->caseToManual($case, 'Flight quote not updated');
                    $reProtectionCreateService->flightRequestToPending($flightRequest, 'Original quote not declined');
                }

                try {
                    if (empty($flightRequest->getFlightQuoteData()) || !is_array($flightRequest->getFlightQuoteData())) {
                        throw new CheckRestrictionException('FlightQuote is empty/wrong data in FlightRequest/data_json');
                    }
                    if (!$flight = $reProtectionCreateService->getFlightByBookingId($flightRequest->fr_booking_id)) {
                        throw new CheckRestrictionException('Flight not found');
                    }

                    $flightQuote = $flightQuoteManageService->createReProtection(
                        $flight,
                        $flightRequest->getFlightQuoteData(),
                        $order->getId(),
                        $flightRequest->fr_booking_id
                    );
                    if ($oldProductQuote) {
                        $relation = ProductQuoteRelation::createReProtection($oldProductQuote->pq_id, $flightQuote->fq_product_quote_id);
                        $productQuoteRelationRepository->save($relation);
                    }
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'New quote not created');
                    throw new CheckRestrictionException($throwable->getMessage());
                }
            } else {
                try {
                    $saleSearch = $casesSaleService->getSaleFromBo($flightRequest->fr_booking_id);

                    if (empty($saleSearch['saleId'])) {
                        throw new BoResponseException('Sale not found by Booking ID(' . $flightRequest->fr_booking_id . ') from "cs/search"');
                    }
                    $saleData = $casesSaleService->detailRequestToBackOffice($saleSearch['saleId'], 0, 120, 1);

                    $orderCreateFromSaleForm = new OrderCreateFromSaleForm();
                    if (!$orderCreateFromSaleForm->load($saleData)) {
                        throw new \DomainException('OrderCreateFromSaleForm not loaded');
                    }
                    if (!$orderCreateFromSaleForm->validate()) {
                        throw new ValidationException(ErrorsToStringHelper::extractFromModel($orderCreateFromSaleForm));
                    }
                    $orderContactForm = OrderContactForm::fillForm($saleData);
                    if (!$orderContactForm->validate()) {
                        throw new ValidationException(ErrorsToStringHelper::extractFromModel($orderContactForm));
                    }

                    $casesSaleService->createSaleByData($case->cs_id, $saleData);
                    $order = $reProtectionCreateService->createOrder($orderCreateFromSaleForm, $orderContactForm, $case);

                    $oldProductQuote = $reProtectionCreateService->createFlightInfrastructure($orderCreateFromSaleForm, $saleData, $order);
                    $oldProductQuote->declined();
                    $productQuoteRepository->save($oldProductQuote);

                    $reProtectionCreateService->createPayment($orderCreateFromSaleForm, $saleData, $order);

                    $client = $reProtectionCreateService->getOrCreateClient($orderCreateFromSaleForm, $orderContactForm);
                    $reProtectionCreateService->additionalFillingCase($case, $client->id, $orderCreateFromSaleForm->getProjectId());
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'Order not created');
                    throw new CheckRestrictionException($throwable->getMessage());
                }

                try {
                    if (empty($flightRequest->getFlightQuoteData()) || !is_array($flightRequest->getFlightQuoteData())) {
                        throw new CheckRestrictionException('FlightQuote empty/wrong data in FlightRequest/data_json');
                    }
                    if (!$flight = $reProtectionCreateService->getFlightByBookingId($flightRequest->fr_booking_id)) {
                        throw new CheckRestrictionException('Flight not found');
                    }

                    $flightQuote = $flightQuoteManageService->createReProtection(
                        $flight,
                        $flightRequest->getFlightQuoteData(),
                        $order->getId(),
                        $flightRequest->fr_booking_id
                    );
                    if ($oldProductQuote) {
                        $relation = ProductQuoteRelation::createReProtection($oldProductQuote->pq_id, $flightQuote->fq_product_quote_id);
                        $productQuoteRelationRepository->save($relation);
                    }
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'New quote not created');
                    throw new CheckRestrictionException($throwable->getMessage());
                }
            }

            if ($flightRequest->getIsAutomateDataJson() === false && $case->isAutomate()) {
                $reProtectionCreateService->caseToManual($case, 'Manual processing requested');
                $reProtectionCreateService->flightRequestToPending($flightRequest, 'Manual processing requested');
            }

            try {
                $hybridService = Yii::createObject(HybridService::class);
                $data = [
                    'bookingId' => $flightRequest->fr_booking_id,
                    'reProtectionProductQuoteGid' => $flightQuote->fqProductQuote->pq_gid,
                    'caseGid' => $case->cs_gid,
                ];
                $hybridService->whReprotection($order->or_project_id, $data);
            } catch (\Throwable $throwable) {
                $reProtectionCreateService->caseToManual($case, 'OTA site is not informed');
                throw new CheckRestrictionException($throwable->getMessage());
            }

            $reProtectionCreateService->setCaseDeadline($case, $flightQuote);

            if ($case->isAutomate()) {
                try {
                    /* TODO::  */
                    // Если кейс еще помечен для автообработки отправить письмо с альтернативной квотой клиенту
                    // в случае успеха перевести Case в статус Auto Processing
                    $flightRequest->statusToDone();
                    \Yii::info(
                        $flightQuote->serialize(),
                        'info\Debug:' . self::class . ':' . __FUNCTION__
                    );
                    /* TODO: FOR DEBUG:: must by remove */
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'Auto SCHD Email not sent');
                    $reProtectionCreateService->flightRequestToPending($flightRequest, $throwable->getMessage());
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable, YII_DEBUG),
                'ReprotectionCreateJob:throwable'
            );
            if (isset($flightRequest)) {
                $reProtectionCreateService->flightRequestToError($flightRequest, $throwable->getMessage());
            }
        }
    }
}
