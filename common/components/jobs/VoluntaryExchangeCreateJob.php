<?php

namespace common\components\jobs;

use common\models\CaseSale;
use DomainException;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\SendEmailVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchange\service\BoRequestVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeHandler;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeService as CaseService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use sales\entities\cases\CaseEventLog;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
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
     *
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
            $voluntaryExchangeService =  new VoluntaryExchangeService($objectCollection);
            $flightRequestService = new FlightRequestService($flightRequest, $objectCollection);

            if (!$case = CaseService::getLastActiveCaseByBookingId($flightRequest->fr_booking_id)) {
                $case = CaseService::createCase(
                    $flightRequest->fr_booking_id,
                    $flightRequest->fr_project_id,
                    true,
                    $objectCollection
                );
            }
            $caseHandler = new CaseVoluntaryExchangeHandler($case, $objectCollection);

            $originProductQuote = VoluntaryExchangeCreateService::getOriginProductQuote($flightRequest->fr_booking_id);

            if (!$originProductQuote) {
                try {
                    $saleData = $boRequestService->getSaleData($flightRequest->fr_booking_id, $case, CaseEventLog::VOLUNTARY_EXCHANGE_CREATE);

                    if ($caseSale = CaseSale::findOne(['css_cs_id' => $case->cs_id, 'css_sale_id' => $saleData['saleId']])) {
                        $caseSale->delete();
                    }
                    $voluntaryExchangeService->createCaseSale($saleData, $case);
                } catch (Throwable $throwable) {
                    $caseHandler->caseToPendingManual('Case sale not created');
                    throw $throwable;
                }

                try {
                    $client = $voluntaryExchangeService->getOrCreateClient(
                        $flightRequest->fr_project_id,
                        $boRequestService->getOrderContactForm()
                    );
                    $caseHandler->addClient($client->id);
                } catch (\Throwable $throwable) {
                    $caseHandler->caseToPendingManual('Client not created');
                    throw $throwable;
                }

                try {
                    $order = $voluntaryExchangeService->createOrder(
                        $boRequestService->getOrderCreateFromSaleForm(),
                        $boRequestService->getOrderContactForm(),
                        $case,
                        $flightRequest->fr_project_id
                    );
                } catch (\Throwable $throwable) {
                    $caseHandler->caseToPendingManual('Order not created');
                    throw $throwable;
                }

                try {
                    $originProductQuote = $voluntaryExchangeService->createOriginProductQuoteInfrastructure(
                        $boRequestService->getOrderCreateFromSaleForm(),
                        $saleData,
                        $order,
                        $case
                    ); /* TODO:: to BOOK */
                } catch (\Throwable $throwable) {
                    $caseHandler->caseToPendingManual('OriginProductQuote not created');
                    throw $throwable;
                }

                try {
                    $caseHandler->setCaseDeadline($originProductQuote->flightQuote);
                } catch (\Throwable $throwable) {
                    \Yii::warning(AppHelper::throwableLog($throwable), 'VoluntaryExchangeCreateJob:setCaseDeadline');
                }
            }

            try {
                $productQuoteChange = $voluntaryExchangeService->createProductQuoteChange($originProductQuote->pq_id, $case->cs_id);
                /* TODO:: to pending */
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('ProductQuoteChange not created');
                throw $throwable;
            }

            try {
                $voluntaryExchangeService->originProductQuoteDecline($originProductQuote, $case);
            } catch (Throwable $throwable) {
                $caseHandler->caseToPendingManual('OriginProductQuote not declined');
                throw $throwable;
            }

            try {
                if (empty($flightRequest->fr_data_json)) {
                    throw new \RuntimeException('FlightRequest data_json cannot be empty');
                }
                $flightProductQuoteData = JsonHelper::decode($flightRequest->fr_data_json);
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('FlightRequest data_json is empty or corrupted');
                throw $throwable;
            }

            try {
                $flight = $voluntaryExchangeService->getFlightByOriginQuote($originProductQuote);
                $flightQuote = $flightQuoteManageService->createVoluntaryExchange(
                    $flight,
                    $flightProductQuoteData,
                    $originProductQuote->pq_order_id,
                    $case,
                    null,
                    $originProductQuote
                );
                $caseHandler->setCaseDeadline($flightQuote);
                $voluntaryExchangeQuote = $flightQuote->fqProductQuote;
                $voluntaryExchangeService->addQuoteGidToDataJson($productQuoteChange, $voluntaryExchangeQuote->pq_gid);
                $voluntaryExchangeService->recommendedExchangeQuote($originProductQuote->pq_id, $voluntaryExchangeQuote->pq_id);
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('Could not create new Voluntary Exchange quote');
                throw $throwable;
            }

            /* TODO:: paymentRequest processing */

            /* TODO:: billingInfo processing */

            try {
                $voluntaryExchangeCreateForm = new VoluntaryExchangeCreateForm();
                if (!$voluntaryExchangeCreateForm->load(ArrayHelper::toArray($flightRequest->fr_data_json))) {
                    throw new \RuntimeException('VoluntaryExchangeCreateForm not loaded');
                }
                if (!$voluntaryExchangeCreateForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($voluntaryExchangeCreateForm));
                }
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('FlightRequest json data is corrupted');
                throw $throwable;
            }

            $caseHandler->caseToAutoProcessing();
            $productQuoteChange->decisionPending();
            $objectCollection->getProductQuoteChangeRepository()->save($productQuoteChange);
            $flightRequestService->done('FlightRequest successfully processed');
        } catch (Throwable $throwable) {
            if (isset($flightRequestService)) {
                $flightRequestService->error($throwable->getMessage());
            }

            if (isset($case, $flightRequest, $voluntaryExchangeService, $caseHandler) && !isset($client)) {
                $client = $voluntaryExchangeService->createSimpleClient($flightRequest->fr_project_id);
                $caseHandler->addClient($client->id);
            }

            /* TODO:: Case to Error + PQC to Error */

            $data['flightRequest'] = $this->flight_request_id;
            VoluntaryExchangeService::writeLog($throwable, 'VoluntaryExchangeCreateJob:throwable', $data);
        }
    }
}
