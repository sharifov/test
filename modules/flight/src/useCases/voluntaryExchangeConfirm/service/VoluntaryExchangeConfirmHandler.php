<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\service;

use common\components\purifier\Purifier;
use common\models\CaseSale;
use common\models\Notifications;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeHandler;
use modules\flight\src\useCases\voluntaryExchange\service\CleanDataVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchangeConfirm\form\VoluntaryExchangeConfirmForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationRepository;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use Yii;

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

    /**
     * @param FlightRequest $flightRequest
     * @param VoluntaryExchangeConfirmForm $confirmForm
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        FlightRequest $flightRequest,
        VoluntaryExchangeConfirmForm $confirmForm,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->confirmForm = $confirmForm;
        $this->flightRequest = $flightRequest;
        $this->objectCollection = $voluntaryExchangeObjectCollection;

        $this->case = $confirmForm->getCase();
        $this->originProductQuote = $confirmForm->getOriginQuote();
        $this->voluntaryExchangeQuote = $confirmForm->getOriginQuote();
        $this->productQuoteChange = $confirmForm->getProductQuoteChange();

        $this->flightRequestService = new FlightRequestService($flightRequest, $this->objectCollection);
        $this->caseHandler = new CaseVoluntaryExchangeHandler($this->case, $this->objectCollection);
    }

    public function prepareRequest(): array
    {
        $request['apiKey'] = $this->case->project->api_key;
        $request['bookingId'] = $this->confirmForm->booking_id;
        $request['billing'] = self::mappingBilling($this->confirmForm->getBillingInfoForm());
        $request['payment'] = self::mappingPayment($this->confirmForm->getPaymentRequestForm());
        $request['exchange'] = $this->prepareExchange();
        return $request;
    }

    private function prepareExchange(): array
    {
        $caseSale = $this->getSale();
        $data['cons'] = $caseSale->css_sale_data['consolidator'] ?? null;
        $data['tickets'] = null;
        if ($flightPaxes = $this->voluntaryExchangeQuote->flightQuote->fqFlight->flightPaxes ?? null) {
            foreach ($flightPaxes as $key => $flightPax) {
                $data['tickets'][$key]['firstName'] = $flightPax->fp_first_name;
                $data['tickets'][$key]['lastName'] = $flightPax->fp_last_name;
                $data['tickets'][$key]['paxType'] = $flightPax->fp_pax_type;
                $data['tickets'][$key]['number'] = $flightPax->flightQuoteTicket->fqt_ticket_number ?? null;
                $data['tickets'][$key]['numRef'] = $key + 1 . '.1';
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

        $data['cabin'] = $this->voluntaryExchangeQuote->flightQuote->fqFlight->fl_cabin_class;

        return $data;
    }

    private function getSale(): CaseSale
    {
        if (!$caseSale = CaseSale::findOne(['css_cs_id' => $this->case->cs_id, 'css_sale_book_id' => $this->confirmForm->booking_id])) {
            throw new \RuntimeException('CaseSale not found by case(' . $this->case->cs_id . ') and booking(' . $this->confirmForm->booking_id . ')');
        }
        return $caseSale;
    }

    public function doneProcess(): void
    {
        /* TODO::  */
    }

    public function failProcess(string $description): void
    {
        /* TODO::  */
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
                    $this->order->getId() ?? null,
                    $creditCardId,
                    $paymentMethodId
                );
            } catch (\Throwable $throwable) {
                \Yii::warning(
                    AppHelper::throwableLog($throwable),
                    'VoluntaryExchangeCreateHandler:additionalProcessing:Billing'
                );
            }
        }
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

    public static function mappingBilling(?BillingInfoForm $billingInfoForm): ?array
    {
        if ($billingInfoForm) {
            $data = [
                'address' => $billingInfoForm->address_line1,
                'countryCode' => $billingInfoForm->country_id,
                'country' => $billingInfoForm->country,
                'city' => $billingInfoForm->city,
                'state' => $billingInfoForm->state,
                'zip' => $billingInfoForm->zip,
                'phone' => $billingInfoForm->contact_phone,
                'email' => $billingInfoForm->contact_email
            ];
        }
        return $data ?? null;
    }
    public static function mappingPayment(?PaymentRequestForm $paymentRequestForm): ?array
    {
        if ($paymentRequestForm) {
            $data = [
                'type' => mb_strtoupper($paymentRequestForm->method_key),
                'card' => [
                    'holderName' => $paymentRequestForm->creditCardForm->holder_name,
                    'number' => $paymentRequestForm->creditCardForm->number,
                    'expirationDate' => $paymentRequestForm->creditCardForm->expiration_month . '/' . $paymentRequestForm->creditCardForm->expiration_year,
                    'cvv' => $paymentRequestForm->creditCardForm->cvv
                ]
            ];
        }
        return $data ?? null;
    }
}
