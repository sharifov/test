<?php

namespace common\components\jobs;

use common\models\CaseSale;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\BoRequestVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCaseService as CaseService;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\CaseEventLog;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
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
            $flightQuoteManageService = Yii::createObject(FlightQuoteManageService::class);

            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flight_request_id . ')');
            }
            $voluntaryExchangeCreateService =  new VoluntaryExchangeCreateService($objectCollection);
            $flightRequestService = new FlightRequestService($flightRequest, $objectCollection);

            if (!$case = CaseService::getLastActiveCaseByBookingId($flightRequest->fr_booking_id)) {
                $case = CaseService::createCase($flightRequest->fr_booking_id, $flightRequest->fr_project_id, $objectCollection);
            }
            $caseService = new CaseService($case, $objectCollection);

            try {
                $saleData = $boRequestService->getSaleData($flightRequest->fr_booking_id, $case);

                if ($caseSale = CaseSale::findOne(['css_cs_id' => $case->cs_id, 'css_sale_id' => $saleData['saleId']])) {
                    $caseSale->delete();
                }
                $voluntaryExchangeCreateService->createCaseSale($saleData, $case);
            } catch (Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Case sale not created');
                throw $throwable;
            }

            try {
                $client = $voluntaryExchangeCreateService->getOrCreateClient(
                    $flightRequest->fr_project_id,
                    $boRequestService->getOrderContactForm()
                );
                $caseService->addClient($client->id);
            } catch (\Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Client not created');
                throw $throwable;
            }

            try {
                $order = $voluntaryExchangeCreateService->createOrder(
                    $boRequestService->getOrderCreateFromSaleForm(),
                    $boRequestService->getOrderContactForm(),
                    $case,
                    $flightRequest->fr_project_id
                );
            } catch (\Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Order not created');
                throw $throwable;
            }

            try {
                $originProductQuote = $voluntaryExchangeCreateService->createOriginProductQuoteInfrastructure(
                    $boRequestService->getOrderCreateFromSaleForm(),
                    $saleData,
                    $order,
                    $case
                );
            } catch (\Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'OriginProductQuote not created');
                throw $throwable;
            }

            try {
                $productQuoteChange = $voluntaryExchangeCreateService->createProductQuoteChange($originProductQuote->pq_id, $case->cs_id);
            } catch (\Throwable $throwable) {
                $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'ProductQuoteChange not created');
                throw $throwable;
            }

            try {
                $caseService->setCaseDeadline($originProductQuote->flightQuote);
            } catch (\Throwable $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'VoluntaryExchangeCreateJob:setCaseDeadline');
            }

            try {
                $voluntaryExchangeCreateService->originProductQuoteDecline($originProductQuote, $case);
            } catch (Throwable $throwable) {
                $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'OriginProductQuote not declined');
                throw $throwable;
            }

            if ($flightProductQuoteData = ArrayHelper::getValue($flightRequest, 'fr_data_json.flight_product_quote')) {
                try {
                    $flight = $voluntaryExchangeCreateService->getFlightByOriginQuote($originProductQuote);
                    $flightQuote = $flightQuoteManageService->createVoluntaryExchange(
                        $flight,
                        $flightProductQuoteData,
                        $originProductQuote->pq_order_id,
                        $case,
                        null,
                        $originProductQuote
                    );
                    $caseService->setCaseDeadline($flightQuote);
                    $exchangeQuote = $flightQuote->fqProductQuote;
                    $voluntaryExchangeCreateService->recommendedExchangeQuote($originProductQuote->pq_id, $exchangeQuote->pq_id);
                } catch (\Throwable $throwable) {
                    $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Could not create new Voluntary Exchange quote');
                    throw $throwable;
                }
            }

            $flightRequestService->done('FlightRequest successfully processed');
        } catch (Throwable $throwable) {
            if (isset($flightRequestService)) {
                $flightRequestService->error($throwable->getMessage());
            }

            if (isset($case, $flightRequest, $voluntaryExchangeCreateService, $caseService) && !isset($client)) {
                $client = $voluntaryExchangeCreateService->createSimpleClient($flightRequest->fr_project_id);
                $caseService->addClient($client->id);
            }

            $data['flightRequest'] = $this->flight_request_id;
            VoluntaryExchangeCreateService::writeLog($throwable, $data);
        }
    }
}
