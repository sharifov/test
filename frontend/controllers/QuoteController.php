<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\Notifications;
use common\models\Quote;
use common\models\QuotePrice;
use frontend\helpers\JsonHelper;
use frontend\helpers\QuoteHelper;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use PhpParser\Node\Expr\Empty_;
use sales\auth\Auth;
use sales\forms\api\searchQuote\FlightQuoteSearchForm;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\socket\ClientChatSocketCommands;
use sales\forms\quotePrice\AddQuotePriceForm;
use sales\forms\segment\SegmentBaggageForm;
use sales\helpers\quote\BaggageHelper;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\services\parsingDump\BaggageService;
use sales\services\parsingDump\lib\ParsingDump;
use sales\services\parsingDump\ReservationService;
use sales\services\quote\addQuote\guard\GdsByQuoteGuard;
use sales\services\quote\addQuote\guard\LeadGuard;
use sales\services\quote\addQuote\price\PreparePrices;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use common\models\Employee;
use common\components\SearchService;
use common\models\QuoteTrip;
use yii\helpers\VarDumper;
use common\models\QuoteSegment;
use common\models\QuoteSegmentStop;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * Quotes controller
 */
class QuoteController extends FController
{
    private const RUNTIME_ERROR_QUOTES_NO_RESULTS = 100;

    /**
     * @param $leadId
     * @return string
     * @throws \Exception
     */
    public function actionGetOnlineQuotes($leadId): string
    {
        try {
            $lead = Lead::findOne(['id' => $leadId]);
            if (Yii::$app->request->isPost) {
                $gds = Yii::$app->request->post('gds', '');

                if ($lead !== null) {
                    $keyCache = sprintf('quick-search-new-%d-%s-%s', $lead->id, $gds, $lead->generateLeadKey());
                    //Yii::$app->cache->delete($keyCache); // only for debug
                    $result = Yii::$app->cacheFile->get($keyCache);

                    if ($result === false) {
                        $result = SearchService::getOnlineQuotes($lead);
                        if ($result && !empty($result['data']) && empty($result['error'])) {
                            Yii::$app->cacheFile->set($keyCache, $result, 600);
                        }
                    }

                    $viewData = SearchService::getAirlineLocationInfo($result['data'] ?? []);
                    $viewData['result'] = $result;
                    $viewData['leadId'] = $leadId;
                    $viewData['gds'] = $gds;
                    $viewData['lead'] = $lead;

                    $result = $this->renderAjax('_search_results', $viewData);
                } else {
                    throw new \Exception('Not found lead', -1);
                }
            } else {
                throw new \Exception('Post is required', -2);
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteController:actionGetOnlineQuotes');
            $result = $throwable->getMessage();
        }
        return $result;
    }

    /**
     * @param $leadId
     * @return string
     * @throws \Exception
     */
    public function actionAjaxSearchQuotes($leadId): string
    {
        try {
            $lead = Lead::findOne(['id' => $leadId]);
            $gds = Yii::$app->request->post('gds', '');
//            $pjaxId = Yii::$app->request->post('pjaxId', '');

            if ($lead !== null) {
                $keyCache = sprintf('quote-search-%d-%s-%s', $lead->id, $gds, $lead->generateLeadKey());

                $quotes = \Yii::$app->cacheFile->get($keyCache);

                if ($quotes === false) {
                    $quotes = SearchService::getOnlineQuotes($lead);
                    if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                        \Yii::$app->cacheFile->set($keyCache, $quotes = QuoteHelper::formatQuoteData($quotes['data']), 600);
                    } else {
                        throw new \RuntimeException(!empty($quotes['error']) ? JsonHelper::decode($quotes['error'])['Message'] : 'No search results', self::RUNTIME_ERROR_QUOTES_NO_RESULTS);
                    }
                }
//                VarDumper::dump($quotes);die;

                $form = new FlightQuoteSearchForm();
                $form->load(Yii::$app->request->post() ?: Yii::$app->request->get());


                if (Yii::$app->request->isPost) {
                    $params = ['page' => 1];
                }

                $viewData = SearchService::getAirlineLocationInfo($quotes);

                $quotes = $form->applyFilters($quotes);
//                VarDumper::dump(ArrayHelper::toArray($form));die;

                $dataProvider = new ArrayDataProvider([
                    'allModels' => $quotes['results'] ?? [],
                    'pagination' => [
                        'pageSize' => 10,
                        'params' => array_merge(Yii::$app->request->get(), $form->getFilters(), $params ?? []),
                    ],
                    'sort' => [
                        'attributes' => ['price', 'duration'],
                        'defaultOrder' => [$form->getSortBy() => $form->getSortType()],
                    ],
                ]);

                $viewData['leadId'] = $leadId;
                $viewData['gds'] = $gds;
                $viewData['lead'] = $lead;
                $viewData['quotes'] = $quotes;
                $viewData['dataProvider'] = $dataProvider ?? new ArrayDataProvider();
                $viewData['searchForm'] = $form;
                $viewData['keyCache'] = $keyCache;
//                    $viewData['pjaxId'] = $pjaxId;
            } else {
                throw new \Exception('Not found lead', -1);
            }
        } catch (\Throwable $throwable) {
            if ($throwable->getCode() !== self::RUNTIME_ERROR_QUOTES_NO_RESULTS) {
                AppHelper::throwableLogger($throwable, 'QuoteController:actionAjaxSearchQuotes');
                $viewData['errorMessage'] = $throwable->getMessage();
            }
            $viewData['quotes'] = [];
        }
        return $this->renderAjax('partial/_quote_search_result', $viewData);
    }

    /**
     * @param $leadId
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCreateQuoteFromSearch($leadId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'error' => '',
            'status' => false
        ];

        $lead = Lead::findOne(['id' => $leadId]);
        if (Yii::$app->request->isPost) {
            //$gds = Yii::$app->request->post('gds');
            $gds = '';
            $key = Yii::$app->request->post('key');
            $keyCache = Yii::$app->request->post('keyCache', '');
            $createFromQuoteSearch = Yii::$app->request->post('createFromQuoteSearch', 0);

            if ($key && $lead) {
                $keyCache = empty($keyCache) ? sprintf('quick-search-new-%d-%s-%s', $lead->id, $gds, $lead->generateLeadKey()) : $keyCache;
                $resultSearch = Yii::$app->cacheFile->get($keyCache);

                if ($resultSearch !== false) {
                    $result = $keyCache && $createFromQuoteSearch ? $resultSearch['results'] : $resultSearch['data']['results'];
                    foreach ($result as $entry) {
                        if ($entry['key'] == $key) {
                            $transaction = Quote::getDb()->beginTransaction();

                            $quote = new Quote();
                            $quote->uid = uniqid();
                            $quote->lead_id = $leadId;
                            $quote->cabin = $lead->cabin;
                            $quote->trip_type = $lead->trip_type;
                            $quote->check_payment = ArrayHelper::getValue($entry, 'prices.isCk', true);
                            $quote->fare_type = $entry['fareType'];
                            $quote->gds = $entry['gds'];
                            $quote->pcc = $entry['pcc'];
                            $quote->main_airline_code = $entry['validatingCarrier'];
                            $quote->last_ticket_date = $entry['prices']['lastTicketDate'];
                            $quote->reservation_dump = str_replace('&nbsp;', ' ', SearchService::getItineraryDump($entry));
                            $quote->employee_id = Yii::$app->user->id;
                            $quote->employee_name = Yii::$app->user->identity->username;
                            $quote->origin_search_data = json_encode($entry);
                            $quote->gds_offer_id = $entry['gdsOfferId'] ?? null;
                            $quote->setMetricLabels(['action' => 'created', 'type_creation' => 'search']);

                            if (isset($entry['tickets'])) {
                                $quote->tickets = json_encode($entry['tickets']);
                            }

                            if ($lead->originalQuoteExist()) {
                                $quote->alternative();
                            } else {
                                $quote->base();
                            }

                            if (!$quote->save()) {
                                $result['error'] = VarDumper::dumpAsString($quote->errors);
                                Yii::error(VarDumper::dumpAsString($quote->getErrors()), 'QuoteController:create-quote-from-search:quote:save');
                                $transaction->rollBack();
                                return $result;
                            } else {
                                if (isset($entry['trips']) && is_array($entry['trips'])) {
                                    $ticketSegments = $quote->getTicketSegments();

                                    foreach ($entry['trips'] as $tripKey => $tripEntry) {
                                        $tripNr = $tripKey + 1;
                                        $segmentNr = 1;

                                        $trip = new QuoteTrip();
                                        $trip->qt_duration = $tripEntry['duration'];

                                        if (!$trip->validate()) {
                                            $result['error'] = VarDumper::dumpAsString($trip->errors);
                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($trip->getErrors()), 'QuoteController:create-quote-from-search:trip:save');
                                            $transaction->rollBack();
                                            return $result;
                                        }
                                        $quote->link('quoteTrips', $trip);
                                        $keys = [];

                                        if (isset($tripEntry['segments']) && is_array($tripEntry['segments'])) {
                                            foreach ($tripEntry['segments'] as $segmentEntry) {
                                                $segment = new QuoteSegment();
                                                $segment->qs_departure_airport_code = $segmentEntry['departureAirportCode'];
                                                if (isset($segmentEntry['departureAirportTerminal']) && !empty($segmentEntry['departureAirportTerminal'])) {
                                                    $segment->qs_departure_airport_terminal = $segmentEntry['departureAirportTerminal'];
                                                }
                                                $segment->qs_arrival_airport_code = $segmentEntry['arrivalAirportCode'];
                                                if (isset($segmentEntry['arrivalAirportTerminal']) && !empty($segmentEntry['arrivalAirportTerminal'])) {
                                                    $segment->qs_arrival_airport_terminal = $segmentEntry['arrivalAirportTerminal'];
                                                }
                                                $segment->qs_arrival_time = $segmentEntry['arrivalTime'];
                                                $segment->qs_departure_time = $segmentEntry['departureTime'];
                                                $segment->qs_air_equip_type = $segmentEntry['airEquipType'];
                                                $segment->qs_booking_class = $segmentEntry['bookingClass'];
                                                $segment->qs_flight_number = $segmentEntry['flightNumber'];
                                                $segment->qs_fare_code = $segmentEntry['fareCode'];
                                                $segment->qs_duration = $segmentEntry['duration'];
                                                $segment->qs_operating_airline = $segmentEntry['operatingAirline'];
                                                $segment->qs_marketing_airline = $segmentEntry['marketingAirline'];
                                                $segment->qs_cabin = $segmentEntry['cabin'];

                                                if ($ticketSegments && isset($ticketSegments[$tripNr][$segmentNr])) {
                                                    $segment->qs_ticket_id = $ticketSegments[$tripNr][$segmentNr];
                                                }

                                                if (isset($segmentEntry['mileage'])) {
                                                    $segment->qs_mileage = $segmentEntry['mileage'];
                                                }
                                                if (isset($segmentEntry['marriageGroup'])) {
                                                    $segment->qs_marriage_group = $segmentEntry['marriageGroup'];
                                                }
                                                if (isset($segmentEntry['meal'])) {
                                                    $segment->qs_meal = $segmentEntry['meal'];
                                                }
                                                if (isset($segmentEntry['recheckBaggage'])) {
                                                    $segment->qs_recheck_baggage = $segmentEntry['recheckBaggage'];
                                                }

                                                $segment->qs_stop = $segmentEntry['stop'];
                                                $segment->qs_air_equip_type = $segmentEntry['airEquipType'];
                                                $segment->qs_key = '#' . $segmentEntry['flightNumber'] .
                                                    ($segmentEntry['stop'] > 0 ? '(' . $segmentEntry['stop'] . ')' : '') .
                                                    $segmentEntry['departureAirportCode'] . '-' . $segmentEntry['arrivalAirportCode'] . ' ' . $segmentEntry['departureTime'];
                                                $keys[] = $segment->qs_key;

                                                if (!$segment->validate()) {
                                                    $result['error'] = VarDumper::dumpAsString($segment->errors);
                                                    Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($segment->getErrors()), 'QuoteController:create-quote-from-search:segment:save');
                                                    $transaction->rollBack();
                                                    return $result;
                                                }
                                                $trip->link('quoteSegments', $segment);

                                                if (isset($segmentEntry['stops']) && !empty($segmentEntry['stops'])) {
                                                    foreach ($segmentEntry['stops'] as $stopEntry) {
                                                        $stop = new QuoteSegmentStop();
                                                        $stop->qss_location_code = $stopEntry['locationCode'];
                                                        $stop->qss_departure_dt = $stopEntry['departureDateTime'];
                                                        $stop->qss_arrival_dt = $stopEntry['arrivalDateTime'];
                                                        if (isset($stopEntry['duration'])) {
                                                            $stop->qss_duration = $stopEntry['duration'];
                                                        }
                                                        if (isset($stopEntry['elapsedTime'])) {
                                                            $stop->qss_elapsed_time = $stopEntry['elapsedTime'];
                                                        }
                                                        if (isset($stopEntry['equipment'])) {
                                                            $stop->qss_equipment = $stopEntry['equipment'];
                                                        }
                                                        if (!$stop->validate()) {
                                                            $result['error'] = VarDumper::dumpAsString($stop->errors);
                                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($stop->getErrors()), 'QuoteController:create-quote-from-search:stop:save');
                                                            $transaction->rollBack();
                                                            return $result;
                                                        }
                                                        $segment->link('quoteSegmentStops', $stop);
                                                    }
                                                }

                                                if (isset($segmentEntry['baggage'])) {
                                                    foreach ($segmentEntry['baggage'] as $paxCode => $baggageEntry) {
                                                        $baggage = new QuoteSegmentBaggage();
                                                        $baggage->qsb_pax_code = $paxCode;
                                                        if (isset($baggageEntry['airlineCode'])) {
                                                            $baggage->qsb_airline_code = $baggageEntry['airlineCode'];
                                                        }
                                                        if (isset($baggageEntry['allowPieces'])) {
                                                            $baggage->qsb_allow_pieces = $baggageEntry['allowPieces'];
                                                        }
                                                        if (isset($baggageEntry['allowWeight'])) {
                                                            $baggage->qsb_allow_weight = $baggageEntry['allowWeight'];
                                                        }
                                                        if (isset($baggageEntry['allowUnit'])) {
                                                            $baggage->qsb_allow_unit = $baggageEntry['allowUnit'];
                                                        }
                                                        if (isset($baggageEntry['allowMaxWeight'])) {
                                                            $baggage->qsb_allow_max_weight = $baggageEntry['allowMaxWeight'];
                                                        }
                                                        if (isset($baggageEntry['allowMaxSize'])) {
                                                            $baggage->qsb_allow_max_size = $baggageEntry['allowMaxSize'];
                                                        }
                                                        if (isset($baggageEntry['carryOn'])) {
                                                            $baggage->qsb_carry_one = $baggageEntry['carryOn'];
                                                        }
                                                        if (!$baggage->validate()) {
                                                            $result['error'] = VarDumper::dumpAsString($baggage->errors);
                                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($baggage->getErrors()), 'QuoteController:create-quote-from-search:baggage:save');

                                                            $transaction->rollBack();
                                                            return $result;
                                                        }
                                                        $segment->link('quoteSegmentBaggages', $baggage);

                                                        if (isset($baggageEntry['charge']) && !empty($baggageEntry['charge'])) {
                                                            foreach ($baggageEntry['charge'] as $baggageEntryCharge) {
                                                                $baggageCharge = new QuoteSegmentBaggageCharge();
                                                                $baggageCharge->qsbc_pax_code = $paxCode;
                                                                if (isset($baggageEntryCharge['price'])) {
                                                                    $baggageCharge->qsbc_price = $baggageEntryCharge['price'];
                                                                }
                                                                if (isset($baggageEntryCharge['currency'])) {
                                                                    $baggageCharge->qsbc_currency = $baggageEntryCharge['currency'];
                                                                }
                                                                if (isset($baggageEntryCharge['firstPiece'])) {
                                                                    $baggageCharge->qsbc_first_piece = $baggageEntryCharge['firstPiece'];
                                                                }
                                                                if (isset($baggageEntryCharge['lastPiece'])) {
                                                                    $baggageCharge->qsbc_last_piece = $baggageEntryCharge['lastPiece'];
                                                                }
                                                                if (isset($baggageEntryCharge['maxWeight'])) {
                                                                    $baggageCharge->qsbc_max_weight = $baggageEntryCharge['maxWeight'];
                                                                }
                                                                if (isset($baggageEntryCharge['maxSize'])) {
                                                                    $baggageCharge->qsbc_max_size = $baggageEntryCharge['maxSize'];
                                                                }
                                                                if (!$baggageCharge->validate()) {
                                                                    $result['error'] = VarDumper::dumpAsString($baggageCharge->errors);
                                                                    Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($baggageCharge->getErrors()), 'QuoteController:create-quote-from-search:baggage_charge:save');
                                                                    $transaction->rollBack();
                                                                    return $result;
                                                                }
                                                                $segment->link('quoteSegmentBaggageCharges', $baggageCharge);
                                                            }
                                                        }
                                                    }
                                                }

                                                $segmentNr++;
                                            }
                                        }

                                        $trip->qt_key = implode('|', $keys);
                                        if (!$trip->save()) {
                                            $result['error'] = VarDumper::dumpAsString($trip->errors);
                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($trip->getErrors()), 'QuoteController:create-quote-from-search:trip:savekey');
                                            $transaction->rollBack();
                                            return $result;
                                        }
                                    }
                                }

                                foreach ($entry['passengers'] as $paxCode => $paxEntry) {
                                    for ($i = 0; $i < $paxEntry['cnt']; $i++) {
                                        $price = new QuotePrice();
                                        $price->passenger_type = $paxCode;
                                        $price->fare = $paxEntry['baseFare'];
                                        $price->taxes = $paxEntry['baseTax'];
                                        $price->net = $price->fare + $price->taxes;
                                        $price->mark_up = $paxEntry['markup'];
                                        $price->selling = $price->net + $price->mark_up + $price->extra_mark_up;
                                        $price->service_fee = ($quote->check_payment) ? QuotePrice::calculateProcessingFeeAmount($price->selling, (new Quote())->serviceFeePercent) : 0;
                                        $price->selling += $price->service_fee;

                                        if (!$price->validate()) {
                                            $result['error'] = VarDumper::dumpAsString($price->errors);
                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($price->getErrors()), 'QuoteController:create-quote-from-search:price:save');
                                            $transaction->rollBack();
                                            return $result;
                                        }

                                        $quote->link('quotePrices', $price);
                                    }
                                }

                                if ($lead->called_expert) {
                                    $quote->sendUpdateBO();
                                }

                                $chat = ClientChatLead::find()->andWhere(['ccl_lead_id' => $lead->id])->one();
                                if ($chat) {
                                    ClientChatSocketCommands::clientChatAddOfferButton($chat->chat, $lead->id);
                                }
                            }

                            $transaction->commit();


                            $result['status'] = true;

                            return $result;
                        }
                    }
                } else {
                    $result['error'] = 'Not found Quote from Search result from Cache. Please update search request!';
                }
            } else {
                $result['error'] = 'Key or Lead is empty!';
            }
        }

        return $result;
    }


    public function actionDecline()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'errors' => [],
            'status' => false
        ];
        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post();
            if (isset($attr['quotes'])) {
                foreach ($attr['quotes'] as $quote) {
                    $model = Quote::findOne(['uid' => $quote]);
                    if ($model !== null && in_array($model->status, [Quote::STATUS_SEND, Quote::STATUS_CREATED, Quote::STATUS_OPENED])) {
                        $model->status = $model::STATUS_DECLINED;
                        if (!$model->save()) {
                            $result['errors'][] = $model->getErrors();
                        }
                    }
                }
            }
            if ($leadId = (int)$attr['leadId']) {
                $lead = Lead::findOne($leadId);
                $chat = ClientChatLead::find()->andWhere(['ccl_lead_id' => $leadId])->one();
                if ($lead && $chat) {
                    if (!(!$chat->chat->isClosed() && $lead->isExistQuotesForSend())) {
                        ClientChatSocketCommands::clientChatRemoveOfferButton($chat->chat, $leadId);
                    }
                }
            }
        }
        return $result;
    }

    //todo delete
//    public function actionExtraPrice()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $result = [];
//        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
//            $model = new ChangeMarkup();
//            $model->attributes = Yii::$app->request->post();
//            if ($model->validate()) {
//                $result = $model->change();
//            }
//        }
//        return $result;
//    }

    public function actionCalcPrice($quoteId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];

        /**
         * @var $model QuotePrice
         * @var $quote Quote
         */
        $model = new QuotePrice();
        $quote = Quote::findOne([
            'id' => $quoteId
        ]);

        if ($quote === null) {
            $quote = new Quote();
        }

        $quote->attributes = Yii::$app->request->post($quote->formName(), []);
        $lead = Lead::findOne([
            'id' => $quote->lead_id
        ]);
        if ($lead === null) {
            return $result;
        }

        $attr = Yii::$app->request->post($model->formName());
        foreach ($attr as $key => $item) {
            $price = QuotePrice::findOne([
                'id' => $item['id']
            ]);
            if ($price === null) {
                $price = new QuotePrice();
            }
            $price->attributes = $item;
            $price->calculatePrice();

            $result[Html::getInputId($price, '[' . $key . ']mark_up')] = $price->mark_up;
            $result[Html::getInputId($price, '[' . $key . ']selling')] = $price->selling;
            $result[Html::getInputId($price, '[' . $key . ']net')] = $price->net;
            $result[Html::getInputId($price, '[' . $key . ']extra_mark_up')] = $price->extra_mark_up;
            $result[Html::getInputId($price, '[' . $key . ']fare')] = $price->fare;
            $result[Html::getInputId($price, '[' . $key . ']taxes')] = $price->taxes;
            $result[Html::getInputId($price, '[' . $key . ']service_fee')] = $price->service_fee;
            $result[Html::getInputId($price, '[' . $key . ']oldParams')] = $price->oldParams;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function actionPrepareDump(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'status' => 1, 'error' => '', 'prices' => '',
            'reservation_dump' => [], 'segments' => '',
            'validating_carrier' => '',
        ];

        try {
            if (Yii::$app->request->isPost) {
                $leadId = (int) Yii::$app->request->get('lead_id');
                $lead = LeadGuard::guard($leadId);
                $prices = [];

                $post = Yii::$app->request->post();
                $quoteFormName = (new Quote())->formName();

                if (isset($post[$quoteFormName], $post['prepare_dump'])) {
                    $postQuote = $post[$quoteFormName];
                    $gds = GdsByQuoteGuard::guard($postQuote['gds']);

                    $itinerary = [];
                    $reservationService = new ReservationService($gds);
                    $reservationService->parseReservation($post['prepare_dump'], true, $itinerary);
                    if ($reservationService->parseStatus) {
                        if ($response['reservation_dump'] = Quote::createDump($itinerary)) {
                            $baggageService = new BaggageService($gds);
                            $baggageService->setBaggageFromDump($post['prepare_dump']);
                            $segments = $baggageService->attachBaggageToSegments($reservationService->parseResult);

                            $response['segments'] = $this->renderAjax('partial/_segmentRows', [
                                'segments' => $segments,
                                'sourceHeight' => BaggageHelper::getBaggageHeightValues(),
                                'sourceWeight' => BaggageHelper::getBaggageWeightValues(),
                            ]);
                        }
                    }

                    if ($obj = ParsingDump::initClass($gds, ParsingDump::PARSING_TYPE_PRICING)) {
                        if ($pricingData = $obj->parseDump($post['prepare_dump'])) {
                            $response['validating_carrier'] = $pricingData['validating_carrier'];
                            if ($pricesFromDump = $pricingData['prices']) {
                                $prices = PreparePrices::prepareByLeadPax($lead, $pricesFromDump);
                                $response['prices'] = $this->renderAjax('partial/_priceRows', [
                                    'prices' => $prices,
                                    'lead' => $lead,
                                ]);
                            }
                        }
                    }

                    if (self::isFailed($response, $prices)) {
                        $response['status'] = 0;
                        $response['error'] = 'Parse GDS Dump failed';
                    }
                }
            } else {
                throw new BadRequestHttpException('Not found POST request');
            }
        } catch (\Throwable $throwable) {
            $response['status'] = 0;
            $response['error'] = $throwable->getMessage();
            \Yii::error(
                \yii\helpers\VarDumper::dumpAsString($throwable, 10),
                'QuoteController:actionPrepareDump:Throwable'
            );
        }
        return $response;
    }

    /**
     * @return array
     */
    public function actionSaveFromDump(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'status' => 0,
            'errorMessage' => '',
            'errorsPrices' => [],
            'errors' => [],
        ];

        $transaction = Quote::getDb()->beginTransaction();
        try {
            if (Yii::$app->request->isPost) {
                $leadId = (int) Yii::$app->request->get('lead_id');
                if (!$lead = Lead::findOne(['id' => $leadId])) {
                    throw new \DomainException('Lead id(' . $leadId . ') not found', -1);
                }

                $post = Yii::$app->request->post();

                if (isset($post['Quote'])) {
                    $postQuote = $post['Quote'];
                    $postQuote['employee_id'] = Yii::$app->user->id;
                    $postQuote['employee_name'] = Yii::$app->user->identity->username;

                    if (!$gds = ParsingDump::getGdsByQuote($postQuote['gds'])) {
                        throw new \DomainException('This gds(' . $postQuote['gds'] . ') cannot be processed', -2);
                    }

                    $quote = Quote::createQuote($postQuote, $lead->id, $lead->originalQuoteExist());
                    $quote->setMetricLabels(['action' => 'created', 'type_creation' => 'manual']);

                    if ((new ReservationService($gds))->parseReservation($post['prepare_dump'], true, $quote->itinerary)) {
                        $itinerary = Quote::createDump($quote->itinerary);
                    } elseif (
                        !empty($post['reservation_result']) &&
                        (new ReservationService('sabre'))->parseReservation(str_replace('&nbsp;', ' ', $post['reservation_result']), true, $quote->itinerary)
                    ) {
                        $itinerary = Quote::createDump($quote->itinerary);
                    } else {
                        throw new \DomainException('Parse "reservation dump" failed', -3);
                    }

                    $quote->reservation_dump = str_replace('&nbsp;', ' ', implode("\n", $itinerary));

                    if (!$quote->save()) {
                        $response['errors'] = $quote->getErrors();
                        throw new \DomainException('Quote not saved. Error: ' . $quote->getErrorSummary(false)[0], -4);
                    }

                    if (empty($post['QuotePrice'])) {
                        throw new \DomainException('Error: QuotePrice cannot be empty.', -5);
                    }

                    foreach ($post['QuotePrice'] as $key => $quotePrice) {
                        if ($price = new AddQuotePriceForm()) {
                            $price->quote_id = $quote->id;
                            $price->passenger_type = $quotePrice['passenger_type'];
                            $price->fare = $quotePrice['fare'];
                            $price->taxes = $quotePrice['taxes'];
                            $price->net = $quotePrice['net'];
                            $price->mark_up = $quotePrice['mark_up'];
                            $price->selling = $quotePrice['selling'];
                            $price->service_fee = ($quote->check_payment) ? round($price->selling * (new Quote())->serviceFee, 2) : 0;

                            if (!$price->validate()) {
                                $response['errorsPrices'][$key] = $price->getErrors();
                            } else {
                                $quotePrice = QuotePrice::manualCreation($price);
                                $quotePrice->roundAttributesValue();
                                $quotePrice->save(false);
                            }
                        }
                    }

                    if (count($response['errorsPrices'])) {
                        throw new \DomainException('QuotePrice not saved.', -6);
                    }

                    $this->logQuote($quote);
                    $quote->createQuoteTrips();

                    foreach ($post as $postKey => $postValues) {
                        $patternBaggageForm = '/SegmentBaggageForm_([A-Z]{3})([A-Z]{3})\z/';
                        preg_match($patternBaggageForm, $postKey, $iataMatches);
                        if (!isset($iataMatches[2])) {
                            continue;
                        }
                        if (
                            (isset($postValues['baggageData']) && is_array($postValues['baggageData'])) &&
                            $segment = QuoteSegment::getByQuoteAndIata($quote->id, $iataMatches[1], $iataMatches[2])
                        ) {
                            $firstPiece = $lastPiece = 0;

                            foreach ($postValues['baggageData'] as $key => $baggageData) {
                                $segmentBaggageForm = new SegmentBaggageForm();
                                $segmentBaggageForm->segmentId = $segment->qs_id;
                                $segmentBaggageForm->load($baggageData, '');

                                if ($segmentBaggageForm->validate()) {
                                    if ($segmentBaggageForm->type === BaggageService::TYPE_PAID) {
                                        if ($segmentBaggageForm->piece === 1) {
                                            ++$firstPiece;
                                            $lastPiece = $firstPiece;
                                        } else {
                                            $firstPiece = $lastPiece + 1;
                                            $lastPiece = $firstPiece + $segmentBaggageForm->piece - 1;
                                        }
                                        $baggageObj = QuoteSegmentBaggageCharge::creationFromForm($segmentBaggageForm, $firstPiece, $lastPiece);
                                    } else {
                                        $baggageObj = QuoteSegmentBaggage::creationFromForm($segmentBaggageForm);
                                    }
                                    $baggageObj->save(false);
                                }
                            }
                        }
                    }

                    if ($lead->called_expert) {
                        $quote->sendUpdateBO();
                    }

                    $transaction->commit();
                    $response['status'] = 1;
                } else {
                    $response['errorMessage'] = 'POST data Quote required';
                }
            } else {
                throw new \DomainException('POST data required', -6);
            }
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            $response['errorMessage'] = $throwable->getMessage();
            if ($throwable->getCode() > 0) {
                \Yii::error(
                    \yii\helpers\VarDumper::dumpAsString($throwable, 10),
                    'QuoteController:actionSaveFromDump:Throwable'
                );
            }
        }
        return $response;
    }

    public function actionSave($save = false)
    {
        $save = (bool)$save;
        $response = [
            'success' => false,
            'errors' => [],
            'itinerary' => [],
            'save' => $save,
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post();
            if (isset($attr['Quote'])) {
                $quote = empty($attr['Quote']['id'])
                    ? new Quote()
                    : Quote::findOne(['id' => $attr['Quote']['id']]);
                if ($quote->isNewRecord) {
                    $quote->uid = uniqid();
                }
                $changedAttributes = $quote->attributes;
                $changedAttributes['selling'] = ($quote->isNewRecord)
                    ? 0 : $quote->quotePrice()['selling'];
                if ($quote !== null) {
                    $quote->attributes = $attr['Quote'];

                    if (!empty($quote->pricing_info)) {
                        $pricing = $quote->parsePriceDump($quote->pricing_info);

                        if (!empty($pricing)) {
                            if (!empty($pricing['validating_carrier'])) {
                                $quote->main_airline_code = $pricing['validating_carrier'];
                                $response[Html::getInputId($quote, 'main_airline_code')] = $quote->main_airline_code;
                            }
                            $response['pricing'] = $pricing;
                            $response[Html::getInputId($quote, 'gds')] = 'S';

                            if (isset($attr['QuotePrice'])) {
                                foreach ($attr['QuotePrice'] as $key => $quotePrice) {
                                    $price = empty($quotePrice['id'])
                                        ? new QuotePrice()
                                        : QuotePrice::findOne(['id' => $quotePrice['id']]);
                                    if ($price !== null) {
                                        $price->attributes = $quotePrice;
                                        $price->quote_id = $quote->id;

                                        if (isset($pricing['prices'][$price->passenger_type])) {
                                            $price->fare = $pricing['prices'][$price->passenger_type]['fare'];
                                            $price->taxes = $pricing['prices'][$price->passenger_type]['taxes'];
                                            $price->net = $price->fare + $price->taxes;
                                            $price->selling = $price->net + $price->mark_up;
                                        }

                                        $price->oldParams = '';
                                        $price->oldParams = serialize($price->attributes);

                                        $price->toFloat();
                                        $response[Html::getInputId($price, '[' . $key . ']fare')] = $price->fare;
                                        $response[Html::getInputId($price, '[' . $key . ']taxes')] = $price->taxes;
                                        $response[Html::getInputId($price, '[' . $key . ']net')] = $price->net;
                                        $response[Html::getInputId($price, '[' . $key . ']selling')] = $price->selling;
                                    }
                                }
                            }
                        }
                    }

                    if (empty($quote->employee_id)) {
                        $quote->employee_id = Yii::$app->user->id;
                        $quote->employee_name = Yii::$app->user->identity->username;
                    } else {
                        if (empty((int)$quote->employee_id)) {
                            $quote->employee_name = $quote->employee_id;
                            $quote->employee_id = null;
                            $quote->created_by_seller = false;
                        } else {
                            $employee = Employee::findIdentity($quote->employee_id);
                            $quote->employee_name = (!empty($employee)) ? $employee->username : Yii::$app->user->identity->username;
                        }
                    }

                    $lead = Lead::findOne(['id' => $quote->lead_id]);
                    if (isset($attr['QuotePrice']) && $lead !== null) {
                        $response['success'] = $quote->validate();
                        if ($save) {
                            $selling = 0;
                            $itinerary = $quote::createDump($quote->itinerary);
                            $quote->reservation_dump = str_replace('&nbsp;', ' ', implode("\n", $itinerary));

                            if ($quote->isNewRecord) {
                                if ($lead->originalQuoteExist()) {
                                    $quote->alternative();
                                } else {
                                    $quote->base();
                                }
                            }

                            if ($quote->save(false)) {
                                if ($lead->called_expert) {
                                    $quote->sendUpdateBO();
                                }
                                $chat = ClientChatLead::find()->andWhere(['ccl_lead_id' => $lead->id])->one();
                                if ($chat) {
                                    ClientChatSocketCommands::clientChatAddOfferButton($chat->chat, $lead->id);
                                }
                            }

                            foreach ($attr['QuotePrice'] as $key => $quotePrice) {
                                $price = empty($quotePrice['id'])
                                ? new QuotePrice()
                                : QuotePrice::findOne(['id' => $quotePrice['id']]);
                                if ($price !== null) {
                                    $price->attributes = $quotePrice;
                                    $price->quote_id = $quote->id;
                                    $price->toFloat();
                                    $selling += $price->selling;
                                    if (!$price->save()) {
                                        $response['itinerary'] = $quote::createDump($quote->itinerary);
                                        $response['errorsPrices'][$key] = $price->getErrors();
                                    }
                                }
                            }

                            if (isset($response['errorsPrices'])) {
                                $response['success'] = false;
                                $quote->delete();
                                return $response;
                            }


                            //Add logs after changed model attributes

                            // todo
//                            $leadLog = new LeadLog((new LeadLogMessage()));
//                            $leadLog->logMessage->oldParams = $changedAttributes;
//                            $newParams = array_intersect_key($quote->attributes, $changedAttributes);
//                            $newParams['selling'] = round($selling, 2);
//                            $leadLog->logMessage->newParams = $newParams;
//                            $leadLog->logMessage->title = ($quote->isNewRecord) ? 'Create' : 'Update';
//                            $leadLog->logMessage->model = sprintf('%s (%s)', $quote->formName(), $quote->uid);
//                            $leadLog->addLog([
//                                'lead_id' => $quote->lead_id,
//                            ]);

                            (\Yii::createObject(GlobalLogInterface::class))->log(
                                new LogDTO(
                                    get_class($quote),
                                    $quote->id,
                                    \Yii::$app->id,
                                    Auth::id(),
                                    Json::encode(['selling' => $changedAttributes['selling'] ?? 0]),
                                    Json::encode(['selling' => round($selling, 2)]),
                                    null,
                                    GlobalLog::ACTION_TYPE_UPDATE
                                )
                            );

                            $quote->createQuoteTrips();

                            if (!empty($quote->pricing_info)) {
                                $quoteAttributes = $quote->parsePriceDump($quote->pricing_info);
                                //var_dump($quoteAttributes['baggage']);die;

                                if (isset($quoteAttributes['baggage']) && !empty($quoteAttributes['baggage'])) {
                                    foreach ($quoteAttributes['baggage'] as $baggageAttr) {
                                        $segmentKey = $baggageAttr['segment'];
                                        $origin = substr($segmentKey, 0, 3);
                                        $destination = substr($segmentKey, 2, 3);
                                        $segment = QuoteSegment::find()->innerJoin(QuoteTrip::tableName(), 'qs_trip_id = qt_id')
                                        ->andWhere(['qt_quote_id' =>  $quote->id])
                                        ->andWhere(['or',
                                            ['qs_departure_airport_code' => $origin],
                                            ['qs_arrival_airport_code' => $destination]
                                        ])
                                        ->one();
                                        $segments = [];
                                        if (!empty($segment)) {
                                            $segments = QuoteSegment::find()
                                            ->andWhere(['qs_trip_id' =>  $segment->qs_trip_id])
                                            ->all();
                                        }
                                        if (!empty($segments)) {
                                            if (isset($baggageAttr['free_baggage']) && isset($baggageAttr['free_baggage']['piece'])) {
                                                foreach ($segments as $segment) {
                                                    $baggage = new QuoteSegmentBaggage();
                                                    $baggage->qsb_allow_pieces = $baggageAttr['free_baggage']['piece'];
                                                    $baggage->qsb_segment_id = $segment->qs_id;
                                                    if (isset($baggageAttr['free_baggage']['weight'])) {
                                                        $baggage->qsb_allow_max_weight = substr($baggageAttr['free_baggage']['weight'], 0, 100);
                                                    }
                                                    if (isset($baggageAttr['free_baggage']['height'])) {
                                                        $baggage->qsb_allow_max_size = substr($baggageAttr['free_baggage']['height'], 0, 100);
                                                    }
                                                    $baggage->save(false);
                                                }
                                            }
                                            if (isset($baggageAttr['paid_baggage']) && !empty($baggageAttr['paid_baggage'])) {
                                                foreach ($segments as $segment) {
                                                    foreach ($baggageAttr['paid_baggage'] as $paidBaggageAttr) {
                                                        $baggage = new QuoteSegmentBaggageCharge();
                                                        $baggage->qsbc_segment_id = $segment->qs_id;
                                                        $baggage->qsbc_price = str_replace('USD', '', $paidBaggageAttr['price']);
                                                        if (isset($paidBaggageAttr['piece'])) {
                                                            $baggage->qsbc_first_piece = $paidBaggageAttr['piece'];
                                                            $baggage->qsbc_last_piece = $paidBaggageAttr['piece'];
                                                        }
                                                        if (isset($paidBaggageAttr['weight'])) {
                                                            $baggage->qsbc_max_weight = substr($paidBaggageAttr['weight'], 0, 100);
                                                        }
                                                        if (isset($paidBaggageAttr['height'])) {
                                                            $baggage->qsbc_max_size = substr($paidBaggageAttr['height'], 0, 100);
                                                        }
                                                        $baggage->save(false);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($lead->called_expert) {
                                $quote = Quote::findOne(['id' => $quote->id]);
                                $data = $quote->getQuoteInformationForExpert(true);
                                $result = BackOffice::sendRequest('lead/update-quote', 'POST', json_encode($data));
                                if ($result['status'] != 'Success' || !empty($result['errors'])) {
                                    Yii::$app->getSession()->setFlash('warning', sprintf(
                                        'Update info quote [%s] for expert failed! %s',
                                        $quote->uid,
                                        print_r($result['errors'], true)
                                    ));
                                }
                            }
                        }
                        $response['itinerary'] = $quote::createDump($quote->itinerary);
                        $response['errors'] = $quote->getErrors();
                    }
                }
            }
        }
        return $response;
    }

    public function actionCreate($leadId, $qId)
    {
        $lead = Lead::findOne(['id' => $leadId]);

        if ($lead !== null) {
            if ($lead->client->isExcluded()) {
                return null;
            }
            $prices = [];
            $quote = new Quote();
            if (empty($qId)) {
                $quote->id = 0;
                $quote->lead_id = $leadId;
                $quote->trip_type = $lead->trip_type;
                $quote->cabin = $lead->cabin;
                $quote->check_payment = true;
                foreach ($lead->getPaxTypes() as $type) {
                    $newQPrice = new QuotePrice();
                    $newQPrice->createQPrice($type);
                    $prices[] = $newQPrice;
                }
            } else {
                $currentQuote = Quote::findOne(['id' => $qId]);
                if ($currentQuote !== null) {
                    $prices = $currentQuote->cloneQuote($quote, $lead);
                }
            }
            if (empty($quote->employee_id)) {
                $quote->employee_id = Yii::$app->user->id;
                $quote->employee_name = Yii::$app->user->identity->username;
            } else {
                $employee = Employee::findIdentity($quote->employee_id);
                $quote->employee_name = (!empty($employee)) ? $employee->username : Yii::$app->user->identity->username;
            }
            return $this->renderAjax('_quote', [
                'lead' => $lead,
                'quote' => $quote,
                'prices' => $prices,
                'project_id' => $lead->project_id
            ]);
        }
        return null;
    }

    public function actionStatusLog($quoteId)
    {
        $quote = Quote::findOne(['id' => $quoteId]);
        if ($quote !== null) {
            return $this->renderAjax('partial/_statusLog', [
                'data' => $quote->getStatusLog(),
            ]);
        }
        return null;
    }

    /**
     * @param string $iata
     * @return array|null
     * @throws BadRequestHttpException|\yii\base\InvalidConfigException
     */
    public function actionSegmentBaggageValidate(string $iata): ?array
    {
        if (Yii::$app->request->isAjax) {
            $segmentBaggageForm = new SegmentBaggageForm($iata);
            $formName = $segmentBaggageForm->formName();
            $post = Yii::$app->request->post();

            $models = [];
            foreach ($post[$formName]['baggageData'] as $key => $value) {
                $model = new SegmentBaggageForm($iata);
                $model->setAttributes($value);
                $models[$key] = $model;
            }

            Model::loadMultiple($models, Yii::$app->request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validateMultiple($models);
        }
        throw new BadRequestHttpException('Not found POST request');
    }

    private function logQuote(Quote $quote): void
    {
        $data = (new Quote())->getDataForProfit($quote->id);
        (\Yii::createObject(GlobalLogInterface::class))->log(
            new LogDTO(
                get_class($quote),
                $quote->id,
                \Yii::$app->id,
                Auth::id(),
                Json::encode(['selling' => 0]),
                Json::encode(['selling' => round($data['selling'], 2)]),
                null,
                GlobalLog::ACTION_TYPE_CREATE
            )
        );
    }

    /**
     * @param array $response
     * @param array|null $prices
     * @return bool
     */
    private static function isFailed(array $response, ?array $prices = null): bool
    {
        return (empty($response['reservation_dump']) && empty($prices));
    }
}
