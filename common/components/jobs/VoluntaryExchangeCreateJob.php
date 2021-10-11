<?php

namespace common\components\jobs;

use common\models\CaseSale;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\BoRequestVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCaseService as CaseService;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\CaseEventLog;
use sales\helpers\ErrorsToStringHelper;
use Throwable;
use Yii;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * @property int|null $flight_request_id
 */
class VoluntaryExchangeCreateJob extends BaseJob implements JobInterface
{
    public $flight_request_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): void
    {
        $this->executionTimeRegister();

        try {
            $objectCollection = Yii::createObject(VoluntaryExchangeObjectCollection::class);
            $boRequestService = Yii::createObject(BoRequestVoluntaryExchangeService::class);

            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flight_request_id . ')');
            }
            $voluntaryExchangeCreateForm = new VoluntaryExchangeCreateForm();
            if (!$voluntaryExchangeCreateForm->load((array) $flightRequest->fr_data_json)) {
                throw new DomainException('VoluntaryExchangeCreateForm not loaded');
            }
            if (!$voluntaryExchangeCreateForm->validate()) {
                throw new \DomainException(ErrorsToStringHelper::extractFromModel($voluntaryExchangeCreateForm));
            }

            $createService =  new VoluntaryExchangeCreateService($voluntaryExchangeCreateForm, $objectCollection);
            $flightRequestService = new FlightRequestService($flightRequest, $objectCollection);

            if (!$case = CaseService::getLastActiveCaseByBookingId($flightRequest->fr_booking_id)) {
                $case = CaseService::createCase($flightRequest->fr_booking_id, $flightRequest->fr_project_id, $objectCollection);
            }
            $caseService = new CaseService($case, $objectCollection);

            /* TODO:: begin processing */
            try {
                $saleData = $boRequestService->getSaleData($flightRequest->fr_booking_id, $case);

                if ($caseSale = CaseSale::findOne(['css_cs_id' => $case->cs_id, 'css_sale_id' => $saleData['saleId']])) {
                    $caseSale->delete();
                }
                $createService->createCaseSale($saleData, $case);
            } catch (Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Case sale not created');
                /* TODO::  */
                return;
            }

            try {
                $client = $createService->getOrCreateClient(
                    $flightRequest->fr_project_id,
                    $boRequestService->getOrderContactForm()
                );
                $caseService->addClient($client->id);
            } catch (\Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Client not created');
            }


            /* TODO:: end processing */
        } catch (Throwable $throwable) {
            $data['flightRequest'] = $this->flight_request_id;
            VoluntaryExchangeCreateService::writeLog($throwable, $data);
        }
    }
}
