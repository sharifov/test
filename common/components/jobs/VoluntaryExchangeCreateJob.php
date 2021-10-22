<?php

namespace common\components\jobs;

use common\models\CaseSale;
use DomainException;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\voluntaryExchange\service\CleanDataVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\OtaRequestVoluntaryRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchange\service\BoRequestVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeHandler;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use Throwable;
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use webapi\src\services\payment\PaymentRequestVoluntaryService;
use Yii;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * @property int|null $flight_request_id
 * @property int|null $case_id
 */
class VoluntaryExchangeCreateJob extends BaseJob implements JobInterface
{
    public $flight_request_id;
    public $case_id;

    /**
     * @param $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        $objectCollection = Yii::createObject(VoluntaryExchangeObjectCollection::class);
        $boRequestService = Yii::createObject(BoRequestVoluntaryExchangeService::class);
        $flightQuoteManageService = Yii::createObject(FlightQuoteManageService::class);
        $paymentRequestVoluntaryService = Yii::createObject(PaymentRequestVoluntaryService::class);
        $voluntaryExchangeService =  new VoluntaryExchangeService($objectCollection);

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flight_request_id . ')');
            }

            $flightRequestService = new FlightRequestService($flightRequest, $objectCollection);

            if (!$case = Cases::findOne(['cs_id' => $this->case_id])) {
                throw new \RuntimeException('Case not found by ID(' . $this->case_id . ')');
            }
            $caseHandler = new CaseVoluntaryExchangeHandler($case, $objectCollection);
            $originProductQuote = VoluntaryExchangeCreateService::getOriginProductQuote($flightRequest->fr_booking_id);

            if ($originProductQuote && ($productQuoteChange = $originProductQuote->productQuoteLastChange)) {
                $voluntaryExchangeService->declineProductQuoteChange($productQuoteChange);
            }

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
                    );
                } catch (\Throwable $throwable) {
                    $caseHandler->caseToPendingManual('OriginProductQuote not created');
                    throw $throwable;
                }

                try {
                    $caseHandler->setCaseDeadline($originProductQuote->flightQuote);
                } catch (\Throwable $throwable) {
                    \Yii::warning(AppHelper::throwableLog($throwable), 'VoluntaryExchangeCreateJob:setCaseDeadline');
                }
            } else {
                $order = $originProductQuote->pqOrder;
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
                $productQuoteChange = $voluntaryExchangeService->createProductQuoteChange(
                    $originProductQuote->pq_id,
                    $case->cs_id,
                    $flightProductQuoteData
                );
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('ProductQuoteChange not created');
                throw $throwable;
            }

            try {
                $voluntaryExchangeService->declineVoluntaryExchangeQuotes($originProductQuote, $case);
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('VoluntaryExchangeQuotes not declined');
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

            try {
                $voluntaryExchangeCreateForm = new VoluntaryExchangeCreateForm();
                if (!$voluntaryExchangeCreateForm->load($flightProductQuoteData)) {
                    throw new \RuntimeException('VoluntaryExchangeCreateForm not loaded');
                }
                if (!$voluntaryExchangeCreateForm->validate(['billing', 'payment_request'])) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($voluntaryExchangeCreateForm));
                }
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('FlightRequest json data is corrupted');
                throw $throwable;
            }

            if (
                !empty($voluntaryExchangeCreateForm->payment_request) &&
                $paymentRequestForm = $voluntaryExchangeCreateForm->getPaymentRequestForm()
            ) {
                try {
                    $paymentRequestVoluntaryService->processing(
                        $paymentRequestForm,
                        $order,
                        'Create by Voluntary Exchange API processing'
                    );
                } catch (\Throwable $throwable) {
                    $caseHandler->caseToPendingManual('PaymentRequest processing is failed');
                    throw $throwable;
                }
            }

            if (
                !empty($voluntaryExchangeCreateForm->billing) &&
                ($billingInfoForm = $voluntaryExchangeCreateForm->getBillingInfoForm())
            ) {
                try {
                    $paymentMethodId = $paymentRequestVoluntaryService->getPaymentMethod()->pm_id ?? null;
                    $creditCardId = $paymentRequestVoluntaryService->getCreditCard()->cc_id ?? null;

                    BillingInfoApiVoluntaryService::getOrCreateBillingInfo(
                        $billingInfoForm,
                        $order->getId(),
                        $creditCardId,
                        $paymentMethodId
                    );
                } catch (\Throwable $throwable) {
                    $caseHandler->caseToPendingManual('BillingInfo create is failed');
                    throw $throwable;
                }
            }

            try {
                (new CleanDataVoluntaryExchangeService($flightRequest, $productQuoteChange, $objectCollection));
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'VoluntaryExchangeCreateJob:CleanDataVoluntaryExchangeService');
            }

            /* TODO:: temporary disable */
            /*
            try {
                OtaRequestVoluntaryRequestService::success($flightRequest, $voluntaryExchangeQuote, $originProductQuote, $case);
            } catch (\Throwable $throwable) {
                $caseHandler->caseToPendingManual('OTA site is not informed');
                throw $throwable;
            }
            */
            \Yii::warning(/* TODO:: FOR DEBUG:: must by remove  */
                'WH to OTA temporary disabled',
                'VoluntaryExchangeCreateJob:OtaRequestVoluntaryRequestService'
            );

            $voluntaryExchangeService->doneProcess(
                $voluntaryExchangeQuote,
                $case,
                $productQuoteChange,
                $flightRequestService
            );
        } catch (Throwable $throwable) {
            if (isset($case, $flightRequest, $caseHandler) && !isset($client)) {
                $client = $voluntaryExchangeService->createSimpleClient($flightRequest->fr_project_id);
                $caseHandler->addClient($client->id);
            }

            $voluntaryExchangeService->failProcess(
                $throwable->getMessage(),
                $case ?? null,
                $productQuoteChange ?? null,
                $flightRequestService ?? null
            );

            $data['flightRequest'] = $this->flight_request_id;
            VoluntaryExchangeService::writeLog($throwable, 'VoluntaryExchangeCreateJob:throwable', $data);
        }
    }
}
