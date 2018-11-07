<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\GTTGlobal;
use common\models\LeadLog;
use common\models\local\ChangeMarkup;
use common\controllers\DefaultController;
use common\models\Lead;
use common\models\local\LeadLogMessage;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\search\QuotePriceSearch;
use common\models\search\QuoteSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use frontend\models\PreviewEmailQuotesForm;
use common\models\Employee;
use common\components\SearchService;
use common\models\QuoteTrip;
use yii\helpers\VarDumper;
use common\models\QuoteSegment;
use common\models\QuoteSegmentStop;
use common\models\QuoteSegmentBaggage;

/**
 * Quotes controller
 */
class QuoteController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'create', 'save', 'decline', 'calc-price', 'extra-price', 'clone',
                            'send-quotes', 'get-online-quotes','get-online-quotes-old','status-log','preview-send-quotes','create-quote-from-search'
                        ],

                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return parent::actions();
    }

    public function actionGetOnlineQuotes($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        if (Yii::$app->request->isPost) {
            $response = [
                'success' => false,
                'body' => ''
            ];
            $attr = Yii::$app->request->post();
            if(isset($attr['gds']) && $lead !== null){
                $keyCache = sprintf('quick-search-new-%d-%d-%s', $lead->id, Yii::$app->user->id, $attr['gds']);
                $result = Yii::$app->cache->get($keyCache);

                if(!$result){
                    $result = SearchService::getOnlineQuotes($lead, $attr['gds']);
                    if($result) {
                        Yii::$app->cache->set($keyCache, $result, 30000);
                    }
                }

                $viewData = SearchService::getAirlineLocationInfo($result);
                $viewData['result'] = $result;
                $viewData['leadId'] = $leadId;
                $viewData['gds'] = $attr['gds'];

                return $this->renderAjax('_search_results', $viewData);
            }

        }

        return '';
    }

    public function actionCreateQuoteFromSearch($leadId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'errors' => [],
            'status' => false
        ];

        $lead = Lead::findOne(['id' => $leadId]);
        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post();

            if(isset($attr['gds']) && isset($attr['key']) && $lead !== null){
                $keyCache = sprintf('quick-search-new-%d-%d-%s', $lead->id, Yii::$app->user->id, $attr['gds']);
                $resultSearch = Yii::$app->cache->get($keyCache);

                if($resultSearch !== false){
                    foreach ($resultSearch['results'] as $entry){
                        if($entry['key'] == $attr['key']){
                            $transaction = Quote::getDb()->beginTransaction();

                            $quote = new Quote();
                            $quote->uid = uniqid();
                            $quote->lead_id = $leadId;
                            $quote->cabin = $lead->cabin;
                            $quote->trip_type = $lead->trip_type;
                            $quote->check_payment = true;
                            $quote->fare_type = $entry['fareType'];
                            $quote->gds = $entry['gds'];
                            $quote->pcc = $entry['pcc'];
                            $quote->main_airline_code = $entry['validatingCarrier'];
                            $quote->last_ticket_date = $entry['prices']['lastTicketDate'];
                            $quote->reservation_dump = str_replace('&nbsp;', ' ', SearchService::getItineraryDump($entry));
                            $quote->employee_id = Yii::$app->user->id;
                            $quote->employee_name = Yii::$app->user->identity->username;

                            if (!$quote->save()) {
                                Yii::error(VarDumper::dump($quote->getErrors()), 'QuoteController:create-quote-from-search:quote:save');
                                $transaction->rollBack();
                                return $result;
                            }else{
                                if(isset($entry['trips']) && is_array($entry['trips'])) {
                                    foreach ($entry['trips'] as $tripEntry){
                                        $trip = new QuoteTrip();
                                        $trip->qt_duration = $tripEntry['duration'];

                                        if(!$trip->validate()){
                                            Yii::error(VarDumper::dumpAsString($entry)."\n".VarDumper::dumpAsString($trip->getErrors()), 'QuoteController:create-quote-from-search:trip:save');
                                            $transaction->rollBack();
                                            return $result;
                                        }
                                        $quote->link('quoteTrips', $trip);
                                        $keys = [];

                                        if(isset($tripEntry['segments']) && is_array($tripEntry['segments'])) {
                                            foreach ($tripEntry['segments'] as $segmentEntry){
                                                $segment = new QuoteSegment();
                                                $segment->qs_departure_airport_code = $segmentEntry['departureAirportCode'];
                                                if(isset($segmentEntry['departureAirportTerminal'])){
                                                    $segment->qs_departure_airport_terminal = $segmentEntry['departureAirportTerminal'];
                                                }
                                                $segment->qs_arrival_airport_code = $segmentEntry['arrivalAirportCode'];
                                                if(isset($segmentEntry['arrivalAirportTerminal'])){
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
                                                $segment->qs_mileage = $segmentEntry['mileage'];
                                                if(isset($segmentEntry['marriageGroup'])){
                                                    $segment->qs_marriage_group = $segmentEntry['marriageGroup'];
                                                }
                                                if(isset($segmentEntry['meal'])){
                                                    $segment->qs_meal = $segmentEntry['meal'];
                                                }
                                                $segment->qs_stop = $segmentEntry['stop'];
                                                $segment->qs_air_equip_type = $segmentEntry['airEquipType'];
                                                $segment->qs_key = '#'.$segmentEntry['flightNumber'].
                                                    ($segmentEntry['stop']>0?'('.$segmentEntry['stop'].')':'').
                                                    $segmentEntry['departureAirportCode'].'-'.$segmentEntry['arrivalAirportCode'].' '.$segmentEntry['departureTime'];
                                                $keys[] = $segment->qs_key;

                                                if(!$segment->validate()){
                                                    Yii::error(VarDumper::dumpAsString($entry)."\n".VarDumper::dumpAsString($segment->getErrors()), 'QuoteController:create-quote-from-search:segment:save');
                                                    $transaction->rollBack();
                                                    return $result;
                                                }
                                                $trip->link('quoteSegments', $segment);

                                                if(isset($segmentEntry['stops']) && !empty($segmentEntry['stops'])){
                                                    foreach ($segmentEntry['stops'] as $stopEntry){
                                                        $stop = new QuoteSegmentStop();
                                                        $stop->qss_location_code = $stopEntry['locationCode'];
                                                        $stop->qss_departure_dt = $stopEntry['departureDateTime'];
                                                        $stop->qss_arrival_dt = $stopEntry['arrivalDateTime'];
                                                        if(isset($stopEntry['duration'])){
                                                            $stop->qss_duration = $stopEntry['duration'];
                                                        }
                                                        if(isset($stopEntry['elapsedTime'])){
                                                            $stop->qss_elapsed_time = $stopEntry['elapsedTime'];
                                                        }
                                                        if(isset($stopEntry['equipment'])){
                                                            $stop->qss_equipment = $stopEntry['equipment'];
                                                        }
                                                        if(!$stop->validate()){
                                                            Yii::error(VarDumper::dumpAsString($entry)."\n".VarDumper::dumpAsString($stop->getErrors()), 'QuoteController:create-quote-from-search:stop:save');
                                                            $transaction->rollBack();
                                                            return $result;
                                                        }
                                                        $segment->link('quoteSegmentStops', $stop);
                                                    }
                                                }

                                                if(isset($segmentEntry['baggage'])){
                                                    foreach ($segmentEntry['baggage'] as $paxCode => $baggageEntry){
                                                        $baggage = new QuoteSegmentBaggage();
                                                        $baggage->qsb_pax_code = $paxCode;
                                                        if(isset($baggageEntry['airlineCode'])){
                                                            $baggage->qsb_airline_code = $baggageEntry['airlineCode'];
                                                        }
                                                        if(isset($baggageEntry['allowPieces'])){
                                                            $baggage->qsb_allow_pieces = $baggageEntry['allowPieces'];
                                                        }
                                                        if(isset($baggageEntry['allowWeight'])){
                                                            $baggage->qsb_allow_weight = $baggageEntry['allowWeight'];
                                                        }
                                                        if(isset($baggageEntry['allowUnit'])){
                                                            $baggage->qsb_allow_unit = $baggageEntry['allowUnit'];
                                                        }
                                                        if(isset($baggageEntry['allowMaxWeight'])){
                                                            $baggage->qsb_allow_max_weight = $baggageEntry['allowMaxWeight'];
                                                        }
                                                        if(isset($baggageEntry['allowMaxSize'])){
                                                            $baggage->qsb_allow_max_size = $baggageEntry['allowMaxSize'];
                                                        }
                                                        if(!$baggage->validate()){
                                                            Yii::error(VarDumper::dumpAsString($entry)."\n".VarDumper::dumpAsString($baggage->getErrors()), 'QuoteController:create-quote-from-search:baggage:save');
                                                            $transaction->rollBack();
                                                            return $result;
                                                        }
                                                        $segment->link('quoteSegmentBaggages', $baggage);
                                                    }
                                                }
                                            }
                                        }

                                        $trip->qt_key = implode('|', $keys);
                                        if(!$trip->save()){
                                            Yii::error(VarDumper::dumpAsString($entry)."\n".VarDumper::dumpAsString($stop->getErrors()), 'QuoteController:create-quote-from-search:trip:savekey');
                                            $transaction->rollBack();
                                            return $result;
                                        }
                                    }
                                }

                                foreach ($entry['passengers'] as $paxCode => $paxEntry){
                                    for($i = 0; $i < $paxEntry['cnt']; $i++){
                                        $price = new QuotePrice();
                                        $price->passenger_type = $paxCode;
                                        $price->fare = $paxEntry['baseFare'];
                                        $price->taxes = $paxEntry['baseTax'];
                                        $price->net = $price->fare + $price->taxes;
                                        $price->mark_up = $paxEntry['markup'];
                                        $price->selling = $paxEntry['price'];

                                        if(!$price->validate()){
                                            Yii::error(VarDumper::dumpAsString($entry)."\n".VarDumper::dumpAsString($baggage->getErrors()), 'QuoteController:create-quote-from-search:baggage:save');
                                            $transaction->rollBack();
                                            return $result;
                                        }

                                        $quote->link('quotePrices', $price);
                                    }
                                }
                            }

                            $transaction->commit();
                            $result['status'] = true;

                            return $result;
                        }
                    }


                }
            }

        }

        return $result;
    }

    public function actionGetOnlineQuotesOld($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        if (Yii::$app->request->isPost) {
            $response = [
                'success' => false,
                'body' => ''
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            $attr = Yii::$app->request->post();
            if (isset($attr['gds']) && $lead !== null) {
                if (isset($attr['itinerary-key']) && !empty($attr['itinerary-key'])) {
                    $routings = Yii::$app->cache->get(sprintf('quick-search-%d-%d', $lead->id, Yii::$app->user->id));
                    $itinerary = null;
                    if ($routings !== false) {
                        foreach ($routings as $routing) {
                            if ($routing['key'] == $attr['itinerary-key']) {
                                $itinerary = $routing;
                                break;
                            }
                        }
                        if ($itinerary !== null) {
                            $prices = [];
                            $model = new Quote();
                            $model->trip_type = $lead->trip_type;
                            $model->check_payment = true;
                            $model->id = 0;
                            $model->lead_id = $lead->id;
                            $model->cabin = $lead->cabin;
                            $model->fare_type = $itinerary['privateFareType'];
                            if(empty($model->employee_id)){
                                $model->employee_id = Yii::$app->user->id;
                                $model->employee_name = Yii::$app->user->identity->username;
                            }else{
                                $employee = Employee::findIdentity($model->employee_id);
                                $model->employee_name = (!empty($employee))?$employee->username:Yii::$app->user->identity->username;
                            }

                            foreach ($lead->getPaxTypes() as $type) {
                                $newQPrice = new QuotePrice();
                                $newQPrice->createQPrice($type);
                                if ($type == QuotePrice::PASSENGER_ADULT) {
                                    $newQPrice->fare = $itinerary['adultBasePrice'];
                                    $newQPrice->taxes = $itinerary['adultTax'];
                                } elseif ($type == QuotePrice::PASSENGER_CHILD) {
                                    $newQPrice->fare = $itinerary['childBasePrice'];
                                    $newQPrice->taxes = $itinerary['childTax'];
                                }
                                $newQPrice->id = 0;
                                $newQPrice::calculation($newQPrice);
                                $newQPrice->toMoney();
                                $prices[] = $newQPrice;
                            }
                            $model->main_airline_code = $itinerary['mainAirlineCode'];
                            $model->reservation_dump = str_replace('&nbsp;', ' ', GTTGlobal::getItineraryDump($itinerary['trips']));
                            $model->gds = $itinerary['gds'];
                            $model->pcc = $itinerary['pcc'];

                            $response['success'] = true;
                            $response['body'] = $this->renderAjax('_quote', [
                                'lead' => $lead,
                                'quote' => $model,
                                'prices' => $prices
                            ]);
                            //Yii::$app->cache->delete(sprintf('quick-search-%d-%d', $lead->id, Yii::$app->user->identity->getId()));
                        }
                    }
                } else {
                    $result = GTTGlobal::getOnlineQuotes($lead, $attr['gds']);
                    $response['success'] = isset($result['airTicketListResponse']);
                    if (isset($result['airTicketListResponse'])) {
                        Yii::$app->cache->set(sprintf('quick-search-%d-%d', $lead->id, Yii::$app->user->id), $result['airTicketListResponse']['routings'], 300);
                        $response['body'] = $this->renderAjax('_onlineQuotesResult', [
                            'alternativeQuotes' => $result['airTicketListResponse']['routings'],
                            'lead' => $lead
                        ]);
                    } else {
                        if (isset($result['hop2WsError'])) {
                            foreach ($result['hop2WsError']['errors'] as $error) {
                                $response['body'] .= $error['message'] . '<br/>';
                            }
                        } else {
                            $response['body'] = 'Internal Server Error';
                        }
                    }
                }
            }
            return $response;
        }

        return $this->renderAjax('_onlineQuotes');
    }

    public function actionSendQuotes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'errors' => [],
            'status' => false
        ];
        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post();
            if (isset($attr['quotes']) && isset($attr['leadId'])) {
                $lead = Lead::findOne(['id' => $attr['leadId']]);
                if ($lead !== null) {
                    $result = $lead->sendEmail($attr['quotes'], $attr['email']);
                    if ($result['status']) {
                        foreach ($attr['quotes'] as $quote) {
                            $model = Quote::findOne(['uid' => $quote]);
                            if ($model !== null && $model->status != $model::STATUS_APPLIED) {
                                $model->status = $model::STATUS_SEND;
                                $model->save();
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function actionPreviewSendQuotes()
    {
        $result = [
            'errors' => [],
            'status' => false
        ];

        $previewEmailModel = new PreviewEmailQuotesForm();
        if (Yii::$app->request->isAjax) {
            $attr = Yii::$app->request->post();

            if (isset($attr['quotes']) && isset($attr['leadId'])) {
                $lead = Lead::findOne(['id' => $attr['leadId']]);
                if ($lead !== null) {
                    $result = $lead->previewEmail($attr['quotes'], $attr['email']);

                    $previewEmailModel->leadId = $attr['leadId'];
                    $previewEmailModel->quotes = implode(',',$attr['quotes']);
                    $previewEmailModel->email = $attr['email'];
                    if(!empty($result['email'])){
                        $previewEmailModel->body = $result['email']['body'];
                        $previewEmailModel->subject = $result['email']['subject'];
                    }
                    return $this->renderAjax('partial/_sendEmail', [
                        'previewEmailModel' => $previewEmailModel,
                        'errors' => $result['errors']
                    ]);
                }
            }
        }elseif (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post($previewEmailModel->formName());
            if (isset($attr['quotes']) && isset($attr['leadId'])) {
                $lead = Lead::findOne(['id' => $attr['leadId']]);
                if ($lead !== null) {
                    $previewEmailModel->attributes = $attr;

                    $result = $previewEmailModel->sendEmail($lead);
                    if ($result['status']) {
                        $quotes = explode(',', $attr['quotes']);

                        foreach ($quotes as $quote){
                            $model = Quote::findOne(['uid' => $quote]);

                            if ($model !== null && $model->status != $model::STATUS_APPLIED) {
                                $model->status = $model::STATUS_SEND;
                                $model->save();
                            }
                        }
                        Yii::$app->getSession()->setFlash('success', sprintf('Sent email \'%s\' succeed.', $previewEmailModel->subject));
                    } else {
                        Yii::$app->getSession()->setFlash('danger', sprintf('Sent email \'%s\' failed. Please verify your email or password from email!', $previewEmailModel->subject));
                    }
                    return $this->redirect([
                        'lead/quote',
                        'type' => 'processing',
                        'id' => $lead->id
                    ]);
                }
            }
        }
        throw new BadRequestHttpException();

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
                    if ($model !== null && in_array($model->status, [$model::STATUS_SEND, $model::STATUS_CREATED])) {
                        $model->status = $model::STATUS_DECLINED;
                        if (!$model->save()) {
                            $result['errors'][] = $model->getErrors();
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function actionExtraPrice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new ChangeMarkup();
            $model->attributes = Yii::$app->request->post();
            if ($model->validate()) {
                $result = $model->change();
            }
        }
        return $result;
    }

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

            $price::calculation($price);
            $price->toMoney();
            $result[Html::getInputId($price, '[' . $key . ']mark_up')] = $price->mark_up;
            $result[Html::getInputId($price, '[' . $key . ']selling')] = $price->selling;
            $result[Html::getInputId($price, '[' . $key . ']net')] = $price->net;
            $result[Html::getInputId($price, '[' . $key . ']extra_mark_up')] = $price->extra_mark_up;
            $result[Html::getInputId($price, '[' . $key . ']fare')] = $price->fare;
            $result[Html::getInputId($price, '[' . $key . ']taxes')] = $price->taxes;
            $result[Html::getInputId($price, '[' . $key . ']oldParams')] = $price->oldParams;
        }

        return $result;
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

                    if(empty($quote->employee_id)){
                        $quote->employee_id = Yii::$app->user->id;
                        $quote->employee_name = Yii::$app->user->identity->username;
                    }else{
                        $employee = Employee::findIdentity($quote->employee_id);
                        $quote->employee_name = (!empty($employee))?$employee->username:Yii::$app->user->identity->username;
                    }

                    $lead = Lead::findOne(['id' => $quote->lead_id]);
                    if (isset($attr['QuotePrice']) && $lead !== null) {
                        $response['success'] = $quote->validate();
                        if ($save) {
                            $itinerary = $quote::createDump($quote->itinerary);
                            $quote->reservation_dump = str_replace('&nbsp;', ' ', implode("\n", $itinerary));
                            $quote->save(false);
                            $selling = 0;
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
                                        var_dump($price->getErrors());
                                    }
                                }
                            }

                            //Add logs after changed model attributes
                            $leadLog = new LeadLog((new LeadLogMessage()));
                            $leadLog->logMessage->oldParams = $changedAttributes;
                            $newParams = array_intersect_key($quote->attributes, $changedAttributes);
                            $newParams['selling'] = round($selling, 2);
                            $leadLog->logMessage->newParams = $newParams;
                            $leadLog->logMessage->title = ($quote->isNewRecord) ? 'Create' : 'Update';
                            $leadLog->logMessage->model = sprintf('%s (%s)', $quote->formName(), $quote->uid);
                            $leadLog->addLog([
                                'lead_id' => $quote->lead_id,
                            ]);

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
            if(empty($quote->employee_id)){
                $quote->employee_id = Yii::$app->user->id;
                $quote->employee_name = Yii::$app->user->identity->username;
            }else{
                $employee = Employee::findIdentity($quote->employee_id);
                $quote->employee_name = (!empty($employee))?$employee->username:Yii::$app->user->identity->username;
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

    public function actionClone($leadId, $qId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $errors = [];

        if ($lead !== null && !empty($qId)) {
            $currentQuote = Quote::findOne(['id' => $qId]);

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_clone', [
                    'lead' => $lead,
                    'quote' => $currentQuote,
                    'errors' => $errors,
                ]);

            }elseif (Yii::$app->request->isPost) {
                $quote = new Quote();
                $quote->attributes = $currentQuote->attributes;
                $quote->uid = uniqid();
                $quote->status = Quote::STATUS_CREATED;
                $quote->save(false);

                $quote->employee_id = $currentQuote->employee_id;
                $quote->update();

                $selling = 0;

                $quotePrices = QuotePrice::findAll(['quote_id' => $qId]);
                foreach ($quotePrices as $price){
                    $newPrice = new QuotePrice();
                    $newPrice->attributes = $price->attributes;
                    $newPrice->quote_id = $quote->id;
                    $newPrice->toFloat();
                    $selling += $newPrice->selling;
                    if (!$newPrice->save()) {
                        $errors = array_merge($errors, $newPrice->getErrors());
                    }
                }
                if(!empty($errors)){
                    return $this->renderAjax('_clone', [
                        'lead' => $lead,
                        'quote' => $currentQuote,
                        'errors' => $errors,
                    ]);
                }

                $changedAttributes = $quote->attributes;
                $changedAttributes['selling'] = 0;

                $leadLog = new LeadLog((new LeadLogMessage()));
                $leadLog->logMessage->oldParams = $changedAttributes;
                $newParams = array_intersect_key($quote->attributes, $changedAttributes);
                $newParams['selling'] = round($selling, 2);
                $leadLog->logMessage->newParams = $newParams;
                $leadLog->logMessage->title = 'Created '.$quote->id.' (Clone from '.$qId.')';
                $leadLog->logMessage->model = sprintf('%s (%s)', $quote->formName(), $quote->uid);
                $leadLog->addLog([
                    'lead_id' => $quote->lead_id,
                ]);

                if ($lead->called_expert) {
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

                return $this->redirect([
                    'lead/quote',
                    'type' => 'processing',
                    'id' => $leadId
                ]);
            }
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
}
