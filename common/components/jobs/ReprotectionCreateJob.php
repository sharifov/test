<?php

namespace common\components\jobs;

use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\reprotectionCreate\service\ReprotectionCreateService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use sales\entities\cases\CaseCategory;
use sales\exception\BoResponseException;
use sales\exception\CheckRestrictionException;
use sales\exception\ValidationException;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\services\cases\CasesSaleService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * @property int $flight_request_id
 */
class ReprotectionCreateJob extends BaseJob implements RetryableJobInterface
{
    public $flight_request_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->executionTimeRegister();
        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new CheckRestrictionException('FlightRequest not found by (' . $this->flight_request_id . ')');
            }

            $casesSaleService = Yii::createObject(CasesSaleService::class);
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

            if (!$caseCategory = CaseCategory::findOne(['cc_key' => ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE])) {
                throw new CheckRestrictionException('CaseCategory (' . ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE . ') is required');
            }

            if ($order = Order::findOne(['or_sale_id' => $orderCreateFromSaleForm->saleId])) {
                /* TODO:: FOR DEBUG:: must by remove  */
                throw new CheckRestrictionException('Order already exist. SaleId(' . $orderCreateFromSaleForm->saleId . ')');
            }

            $reProtectionCreateService = Yii::createObject(ReprotectionCreateService::class);

            $client = $reProtectionCreateService->getOrCreateClient($orderCreateFromSaleForm, $orderContactForm);
            $case = $reProtectionCreateService->createCase($orderCreateFromSaleForm, $client);
            $order = $reProtectionCreateService->createOrder($orderCreateFromSaleForm, $orderContactForm, $case);
            $reProtectionCreateService->createFlight($orderCreateFromSaleForm, $saleData, $order);

            $reProtectionCreateService->createPayment($orderCreateFromSaleForm, $saleData, $order);
            /* TODO::  */
        } catch (BoResponseException $throwable) {
            \Yii::warning(
                AppHelper::throwableLog($throwable),
                'ReprotectionCreateJob:BoResponseException'
            );
            throw new BoResponseException($throwable->getMessage());
        } catch (ValidationException | CheckRestrictionException $throwable) {
            \Yii::warning(
                AppHelper::throwableLog($throwable),
                'ReprotectionCreateJob:ValidationException'
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable, YII_DEBUG),
                'ReprotectionCreateJob:throwable'
            );
        }
        return false;
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 60;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 4);
    }
}
