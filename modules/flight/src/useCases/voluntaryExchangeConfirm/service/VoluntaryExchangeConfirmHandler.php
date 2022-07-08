<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\service;

use common\components\BackOffice;
use common\components\purifier\Purifier;
use common\models\CaseSale;
use common\models\Notifications;
use frontend\helpers\JsonHelper;
use modules\featureFlag\FFlag;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeHandler;
use modules\flight\src\useCases\voluntaryExchange\service\CleanDataVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeConfirm\form\VoluntaryExchangeConfirmForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\service\VoluntaryExchangeBOPrepareService;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\service\VoluntaryExchangeBOService;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use webapi\src\request\BoRequestDataHelper;
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use Yii;
use yii\helpers\ArrayHelper;

use function Amp\Promise\timeoutWithDefault;

/**
 * Class VoluntaryExchangeConfirmHandler
 *
 * @property Cases $case;
 * @property FlightRequest $flightRequest
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property FlightRequestService $flightRequestService
 * @property CaseVoluntaryExchangeHandler $caseHandler
 * @property VoluntaryExchangeConfirmForm $confirmForm
 *
 * @property ProductQuote $originProductQuote
 * @property ProductQuote $voluntaryExchangeQuote
 * @property ProductQuoteChange $productQuoteChange
 * @property Order $order
 */
class VoluntaryExchangeConfirmHandler
{
    private FlightRequest $flightRequest;
    private VoluntaryExchangeConfirmForm $confirmForm;
    private VoluntaryExchangeObjectCollection $objectCollection;

    private FlightRequestService $flightRequestService;
    private CaseVoluntaryExchangeHandler $caseHandler;

    private Cases $case;
    private ?ProductQuote $originProductQuote = null;
    private ?ProductQuote $voluntaryExchangeQuote = null;
    private ?ProductQuoteChange $productQuoteChange = null;
    private ?Order $order = null;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;

    /**
     * @param FlightRequest $flightRequest
     * @param VoluntaryExchangeConfirmForm $confirmForm
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        FlightRequest $flightRequest,
        VoluntaryExchangeConfirmForm $confirmForm,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection,
        ProductQuoteRefundRepository $productQuoteRefundRepository
    ) {
        $this->confirmForm = $confirmForm;
        $this->flightRequest = $flightRequest;
        $this->objectCollection = $voluntaryExchangeObjectCollection;

        $this->case = $confirmForm->getCase();
        $this->originProductQuote = $confirmForm->getOriginQuote();
        $this->voluntaryExchangeQuote = $confirmForm->getChangeQuote();
        $this->productQuoteChange = $confirmForm->getProductQuoteChange();

        $this->flightRequestService = new FlightRequestService($flightRequest, $this->objectCollection);
        $this->caseHandler = new CaseVoluntaryExchangeHandler($this->case, $this->objectCollection);
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
    }

    public function prepareRequest(): array
    {
        $request['apiKey'] = $this->case->project->api_key;
        $request['bookingId'] = $this->confirmForm->booking_id;
        $request['billing'] = BoRequestDataHelper::fillBillingData($this->confirmForm->getBillingInfoForm());
        $request['payment'] = BoRequestDataHelper::fillPaymentData($this->confirmForm->getPaymentRequestForm());
        $request['exchange'] = $this->prepareExchange();

        /** @fflag FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS, Send additional info to BO endpoints enable\disable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS)) {
            $request['additionalInfo'] = BoRequestDataHelper::prepareAdditionalInfoToBoRequest($this->confirmForm->changeQuote);
        }
        return $request;
    }

    private function prepareExchange(): array
    {
        $caseSale = $this->getSale();
        $data['cons'] = $caseSale->css_sale_data['consolidator'] ?? null;
        $data['tickets'] = null;

        if (empty($this->productQuoteChange->pqc_data_json['exchange'])) {
            // request to BO /api/v3/flight-request/get-exchange-data/
            $getParams        = \Yii::$app->request->get();
            $boPrepareService = new VoluntaryExchangeBOPrepareService($this->case->project, $this->originProductQuote);
            try {
                $boPrepareService->fill();
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $getParams);
                \Yii::warning($message, 'VoluntaryExchangeConfirmHandler:prepareExchange:VoluntaryExchangeBOPrepareService');
            }

            $voluntaryExchangeBOService = new VoluntaryExchangeBOService($boPrepareService);
            try {
                $voluntaryExchangeBOService->requestProcessing();
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $getParams);
                \Yii::warning($message, 'VoluntaryExchangeConfirmHandler:prepareExchange:BoGetExchangeData');
            }

            $this->productQuoteChange->pqc_data_json = JsonHelper::encode($voluntaryExchangeBOService->getResult());
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);
        }

        if (!empty($this->productQuoteChange->pqc_data_json['exchange']['tickets'])) {
            foreach ($this->productQuoteChange->pqc_data_json['exchange']['tickets'] as $key => $flightPax) {
                $data['tickets'][$key]['firstName'] = $flightPax['firstName'];
                $data['tickets'][$key]['lastName']  = $flightPax['lastName'];
                $data['tickets'][$key]['paxType']   = $flightPax['paxType'];
                $data['tickets'][$key]['number']    = $flightPax['number'] ?? null;
                $data['tickets'][$key]['numRef']    = $key + 1 . '.1';
            }
        } elseif ($flightPaxes = $this->voluntaryExchangeQuote->flightQuote->fqFlight->flightPaxes ?? null) {
            foreach ($flightPaxes as $key => $flightPax) {
                $data['tickets'][$key]['firstName'] = $flightPax->fp_first_name;
                $data['tickets'][$key]['lastName'] = $flightPax->fp_last_name;
                $data['tickets'][$key]['paxType'] = $flightPax->fp_pax_type;
                $data['tickets'][$key]['number'] = $flightPax->flightQuoteTicket->fqt_ticket_number ?? null;
                $data['tickets'][$key]['numRef'] = $key + 1 . '.1';
            }
        }

        $data['passengers'] = null;
        if ($flightQuotePaxPrices = $this->voluntaryExchangeQuote->flightQuote->flightQuotePaxPrices ?? null) {
            foreach ($flightQuotePaxPrices as $flightQuotePaxPrice) {
                $key = FlightPax::getPaxTypeById($flightQuotePaxPrice->qpp_flight_pax_code_id);
                $data['passengers'][$key]['codeAs'] = FlightPax::getPaxTypeById($flightQuotePaxPrice->qpp_flight_pax_code_id);
                $data['passengers'][$key]['cnt'] = $flightQuotePaxPrice->qpp_cnt;
                $data['passengers'][$key]['baseFare'] = $flightQuotePaxPrice->qpp_fare;
                $data['passengers'][$key]['baseTax'] = $flightQuotePaxPrice->qpp_tax;
                $data['passengers'][$key]['markup'] = $mark_up = $flightQuotePaxPrice->qpp_agent_mark_up + $flightQuotePaxPrice->qpp_system_mark_up;
                $data['passengers'][$key]['price'] = $flightQuotePaxPrice->qpp_fare + $flightQuotePaxPrice->qpp_tax + $mark_up;
            }
        }

        $data['trips'] = null;
        if ($flightQuoteTrips = $this->voluntaryExchangeQuote->flightQuote->flightQuoteTrips ?? null) {
            foreach ($flightQuoteTrips as $keyTrip => $trip) {
                $tripId = $keyTrip + 1;
                $data['trips'][$keyTrip]['tripId'] = $tripId;
                $data['trips'][$keyTrip]['duration'] = (int) $trip->fqt_duration;

                if ($segments = $trip->flightQuoteSegments) {
                    foreach ($segments as $keySegment => $segment) {
                        $segmentId = $keySegment + 1;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['segmentId'] = $segmentId;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['departureTime'] = $segment->fqs_departure_dt;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['arrivalTime'] = $segment->fqs_arrival_dt;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['flightNumber'] = $segment->fqs_flight_number;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['bookingClass'] = $segment->fqs_booking_class;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['duration'] = $segment->fqs_duration;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['departureAirportCode'] = $segment->fqs_departure_airport_iata;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['departureAirportTerminal'] = $segment->fqs_departure_airport_terminal;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['arrivalAirportCode'] = $segment->fqs_arrival_airport_iata;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['arrivalAirportTerminal'] = $segment->fqs_arrival_airport_terminal;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['operatingAirline'] = $segment->fqs_operating_airline;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['airEquipType'] = $segment->fqs_air_equip_type;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['marketingAirline'] = $segment->fqs_marketing_airline;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['marriageGroup'] = $segment->fqs_marriage_group;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['cabin'] = $segment->fqs_cabin_class;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['meal'] = $segment->fqs_meal;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['fareCode'] = $segment->fqs_fare_code;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['recheckBaggage'] = $segment->fqs_recheck_baggage;

                        $data['trips'][$keyTrip]['segments'][$keySegment]['stop'] = $segment->fqs_stop;
                        $data['trips'][$keyTrip]['segments'][$keySegment]['stops'] = [];
                        if ($stops = $segment->flightQuoteSegmentStops) {
                            foreach ($stops as $keyStop => $stop) {
                                $data['trips'][$keyTrip]['segments'][$keySegment]['stops'][$keyStop]['locationCode'] = $stop->qss_location_iata;
                                $data['trips'][$keyTrip]['segments'][$keySegment]['stops'][$keyStop]['departureDateTime'] = $stop->qss_departure_dt;
                                $data['trips'][$keyTrip]['segments'][$keySegment]['stops'][$keyStop]['arrivalDateTime'] = $stop->qss_arrival_dt;
                                $data['trips'][$keyTrip]['segments'][$keySegment]['stops'][$keyStop]['duration'] = $stop->qss_duration;
                                $data['trips'][$keyTrip]['segments'][$keySegment]['stops'][$keyStop]['elapsedTime'] = $stop->qss_elapsed_time;
                                $data['trips'][$keyTrip]['segments'][$keySegment]['stops'][$keyStop]['equipment'] = $stop->qss_equipment;
                            }
                        }
                    }
                }
            }
        }
        $data['currency'] = $this->voluntaryExchangeQuote->pq_client_currency ?: $this->voluntaryExchangeQuote->pq_origin_currency;
        $data['validatingCarrier'] = $this->voluntaryExchangeQuote->flightQuote->fq_main_airline;
        $data['gds'] = $this->voluntaryExchangeQuote->flightQuote->fq_gds;
        $data['pcc'] = $this->voluntaryExchangeQuote->flightQuote->fq_gds_pcc;
        $data['fareType'] = FlightQuote::getFareTypeNameById($this->voluntaryExchangeQuote->flightQuote->fq_fare_type_id);
        if (!empty($this->voluntaryExchangeQuote->flightQuote->fq_record_locator)) {
            $data['pnr'] = $this->voluntaryExchangeQuote->flightQuote->fq_record_locator;
        }

        $data['cabin'] = $this->voluntaryExchangeQuote->flightQuote->fqFlight->fl_cabin_class;

        return $data;
    }

    private function getSale(): CaseSale
    {
        $caseSale = CaseSale::find()
            ->where(['css_cs_id' => $this->case->cs_id])
            ->byBaseBookingId($this->confirmForm->booking_id)
            ->limit(1)
            ->one()
        ;
        if (!$caseSale) {
            throw new \RuntimeException('CaseSale not found by case(' . $this->case->cs_id . ') and booking(' . $this->confirmForm->booking_id . ')');
        }
        return $caseSale;
    }

    public function doneProcess(): void
    {
        $this->case->awaiting(null, 'Voluntary Exchange Confirm api processing');
        $this->objectCollection->getCasesRepository()->save($this->case);

        $this->voluntaryExchangeQuote->inProgress(null, 'Voluntary Exchange Confirm api processing');
        $this->objectCollection->getProductQuoteRepository()->save($this->voluntaryExchangeQuote);

        $this->productQuoteChange->decisionToConfirm()->inProgress();

        $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);

        $this->flightRequestService->done('FlightRequest successfully processed');

        foreach ($this->productQuoteChange->productQuoteChangeRelations as $changeRelation) {
            $relatedProductQuote = $changeRelation->pqcrPq;
            if ($relatedProductQuote->pq_id === $this->voluntaryExchangeQuote->pq_id) {
                continue;
            }
            $relatedProductQuote->cancelled(null, 'Voluntary Exchange Confirm api processing');
            $this->objectCollection->getProductQuoteRepository()->save($relatedProductQuote);
        }

        if ($this->case->cs_user_id) {
            $linkToCase = Purifier::createCaseShortLink($this->case);
            Notifications::createAndPublish(
                $this->case->cs_user_id,
                'Voluntary Exchange Confirm request',
                'Voluntary Exchange request. Case: (' . $linkToCase . ')',
                Notifications::TYPE_INFO,
                true
            );
        }
        $this->addCaseEventLog('Voluntary Exchange Confirm process completed successfully', [
            'productQuoteChangeId' => $this->productQuoteChange->pqc_id,
            'confirmedProductQuoteId' => $this->voluntaryExchangeQuote->pq_id,
            'originalProductQuoteId' => $this->originProductQuote->pq_id,
        ], CaseEventLog::CATEGORY_INFO);
    }

    public function failProcess(string $description): void
    {
        if ($this->case) {
            $this->case->error(null, 'Voluntary Exchange Confirm Api processing fail');
            if ($this->case->isAutomate()) {
                $this->case->offIsAutomate();
            }
            $this->objectCollection->getCasesRepository()->save($this->case);

            $this->addCaseEventLog('Voluntary Exchange Api Confirm processing fail', [], CaseEventLog::CATEGORY_ERROR);
            $this->addCaseEventLog($description, [], CaseEventLog::CATEGORY_DEBUG);

            if ($this->case->cs_user_id) {
                $linkToCase = Purifier::createCaseShortLink($this->case);
                Notifications::createAndPublish(
                    $this->case->cs_user_id,
                    'Voluntary Exchange Confirm request fail',
                    'Error in Voluntary Exchange Confirm request. Case: (' . $linkToCase . ')',
                    Notifications::TYPE_DANGER,
                    true
                );
            }
        }

        if ($this->productQuoteChange) {
            $this->productQuoteChange->error();
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);
        }

        if ($this->flightRequestService) {
            $this->flightRequestService->error($description);
            if (($this->productQuoteChange) && ($flightRequest = $this->flightRequestService->getFlightRequest())) {
                (new CleanDataVoluntaryExchangeService($flightRequest, $this->productQuoteChange, $this->objectCollection));
            }
        }

        if ($this->voluntaryExchangeQuote) {
            $this->voluntaryExchangeQuote->error(null, 'Voluntary Exchange Api Confirm processing fail');
            $this->objectCollection->getProductQuoteRepository()->save($this->voluntaryExchangeQuote);
        }
    }

    public function additionalProcessing(): void
    {
        if (
            !empty($this->confirmForm->payment_request) &&
            $paymentRequestForm = $this->confirmForm->getPaymentRequestForm()
        ) {
            try {
                $this->objectCollection->getPaymentRequestVoluntaryService()->processing(
                    $paymentRequestForm,
                    $this->order,
                    'Create by Voluntary Exchange API processing'
                );
            } catch (\Throwable $throwable) {
                $this->addCaseEventLog('Api Confirm. PaymentRequest not processed', [], CaseEventLog::CATEGORY_WARNING);
                \Yii::warning(
                    AppHelper::throwableLog($throwable),
                    'VoluntaryExchangeCreateHandler:additionalProcessing:PaymentRequest'
                );
            }
        }

        if (
            !empty($this->confirmForm->billing) &&
            ($billingInfoForm = $this->confirmForm->getBillingInfoForm())
        ) {
            try {
                $paymentMethodId = $this->objectCollection->getPaymentRequestVoluntaryService()->getPaymentMethod()->pm_id ?? null;
                $creditCardId = $this->objectCollection->getPaymentRequestVoluntaryService()->getCreditCard()->cc_id ?? null;

                BillingInfoApiVoluntaryService::getOrCreateBillingInfo(
                    $billingInfoForm,
                    $this->order->or_id ?? null,
                    $creditCardId,
                    $paymentMethodId
                );
            } catch (\Throwable $throwable) {
                $this->addCaseEventLog('Api Confirm. BillingInfo not processed', [], CaseEventLog::CATEGORY_WARNING);
                \Yii::warning(
                    AppHelper::throwableLog($throwable),
                    'VoluntaryExchangeCreateHandler:additionalProcessing:Billing'
                );
            }
        }
    }

    /**
     * Cancel all change quotes in statuses NEW and PENDING
     *
     * @return void
     */
    public function cancelChangeQuotes(): void
    {
        $productRefundQuotes = $this->productQuoteRefundRepository->findAllByBookingId(
            $this->flightRequest->fr_booking_id,
            [ProductQuoteRefundStatus::PENDING, ProductQuoteRefundStatus::NEW]
        );
        foreach ($productRefundQuotes as $productRefundQuote) {
            $productRefundQuote->detachBehavior('user');
            $productRefundQuote->cancel();
            $this->productQuoteRefundRepository->save($productRefundQuote);
        }
    }

    public function addCaseEventLog(string $description, array $data = [], int $categoryId = CaseEventLog::CATEGORY_INFO): void
    {
        $this->case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CONFIRM,
            $description,
            $data,
            $categoryId
        );
    }

    public function getOriginProductQuote(): ?ProductQuote
    {
        return $this->originProductQuote;
    }

    public function getVoluntaryExchangeQuote(): ?ProductQuote
    {
        return $this->voluntaryExchangeQuote;
    }

    public function getProductQuoteChange(): ?ProductQuoteChange
    {
        return $this->productQuoteChange;
    }
}
