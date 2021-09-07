<?php

namespace frontend\controllers;

use common\components\SearchService;
use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\QuoteSegment;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;
use common\models\QuoteSegmentStop;
use common\models\QuoteTrip;
use frontend\helpers\QuoteHelper;
use sales\auth\Auth;
use sales\dto\searchService\SearchServiceQuoteDTO;
use sales\forms\api\searchQuote\FlightQuoteSearchForm;
use sales\forms\CompositeFormHelper;
use sales\forms\lead\ItineraryEditForm;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\abac\ClientChatAbacObject;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\socket\ClientChatSocketCommands;
use sales\model\clientChatDataRequest\entity\ClientChatDataRequest;
use sales\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use sales\model\lead\useCases\lead\create\LeadCreateByChatForm;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\leadUserConversion\repository\LeadUserConversionRepository;
use sales\model\leadUserConversion\service\LeadUserConversionDictionary;
use sales\model\quoteLabel\service\QuoteLabelService;
use sales\model\userClientChatData\service\UserClientChatDataService;
use sales\repositories\NotFoundException;
use sales\repositories\quote\QuoteRepository;
use sales\services\lead\LeadManageService;
use sales\viewModel\chat\ViewModelSearchQuotes;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ClientChatFlightQuoteController
 * @package frontend\controllers
 *
 * @property-read LeadManageService $leadManageService
 * @property-read QuoteRepository $quoteRepository
 */
class ClientChatFlightQuoteController extends FController
{
    private LeadManageService $leadManageService;
    private QuoteRepository $quoteRepository;

    public function __construct($id, $module, LeadManageService $leadManageService, QuoteRepository $quoteRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadManageService = $leadManageService;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-search-quotes-by-chat',
                    'view-edit-form',
                    'edit',
                    'validate',
                    'create-quote-from-search'
                ],
            ],
            [
                'class' => ContentNegotiator::class,
                'only' => ['validate'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['validate', 'edit']
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionAjaxSearchQuotesByChat()
    {
        $chatId = Yii::$app->request->get('chat_id', 0);
        $leadId = Yii::$app->request->get('lead_id', 0);
        $flightRequestFormMode = (string)Yii::$app->request->get('mode', 'view');
        /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $chat = ClientChat::findOne(['cch_id' => $chatId]);
        $viewModel = new ViewModelSearchQuotes();

        $viewModel->chatId = (int)$chatId;
        $viewModel->flightRequestFormMode = $flightRequestFormMode;

        try {
            if (!$chat) {
                throw new NotFoundException('Chat not found by id: ' . $chatId);
            }
            $keyCache = 'quote-search-' . $chat->cch_rid;

            $lead = Lead::findOne($leadId);
            if (!$lead) {
                $userId = Auth::id();
                $createLeadByChatForm = new LeadCreateByChatForm($userId, $chat);

                if ($createLeadByChatForm->validate()) {
                    $lead = $this->createLeadAndLinkWithChat($createLeadByChatForm, $chat, $userId);
                    $viewModel->leadCreated = true;
                } else {
                    throw new \RuntimeException('Lead Creation Form Validation Failed: ' . $createLeadByChatForm->getErrorSummary(true)[0]);
                }

                try {
                    $chatDataRequest = ClientChatDataRequest::find()->byChatId($chat->cch_id)->one();
                    if ($chatDataRequest && $chatDataRequest->ccdr_data_json && is_array($chatDataRequest->ccdr_data_json)) {
                        $form = new FlightSearchDataRequestForm($chatDataRequest->ccdr_data_json);

                        $itineraryForm = new ItineraryEditForm($lead, $form->isRoundTrip() ? 2 : 1);
                        $itineraryForm->fillInByChatDataRequestForm($form);

                        if ($itineraryForm->validate()) {
                            $this->leadManageService->editItinerary($lead, $itineraryForm);
                        } else {
                            Yii::$app->getSession()->addFlash(
                                'warning',
                                'Itinerary form validation failed: ' . VarDumper::dumpAsString($itineraryForm->getErrors())
                            );
                        }
                    }
                } catch (\Throwable $e) {
                    Yii::$app->getSession()->addFlash(
                        'warning',
                        'Create flight itinerary failed: ' . $e->getMessage()
                    );
                }

                $searchServiceDto = new SearchServiceQuoteDTO($lead);
                $quotes = Yii::$app->cacheFile->get($keyCache);
                if ($quotes === false) {
                    $quotes = SearchService::getOnlineQuotes($searchServiceDto);

                    $keyCache .= '-lead-' . $lead->id;
                    if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                        Yii::$app->cacheFile->set(
                            $keyCache,
                            $quotes = QuoteHelper::formatQuoteData($quotes['data']),
                            600
                        );
                    }
                }
            } else {
                $searchServiceDto = new SearchServiceQuoteDTO($lead);
                $keyCache .= '-lead-' . $lead->id;
                $quotes = Yii::$app->cacheFile->get($keyCache) ?: [];
                if (!$quotes && !empty($searchServiceDto->fl)) {
                    $quotes = SearchService::getOnlineQuotes($searchServiceDto);

                    if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                        Yii::$app->cacheFile->set(
                            $keyCache,
                            $quotes = QuoteHelper::formatQuoteData($quotes['data']),
                            600
                        );
                    }
                }
            }

            $itineraryForm = new ItineraryEditForm($lead);


            try {
                $flightQuoteSearchForm = new FlightQuoteSearchForm();
                $flightQuoteSearchForm->load(Yii::$app->request->post() ?: Yii::$app->request->get());

                if (Yii::$app->request->isPost) {
                    $params = ['page' => 1];
                }

                $viewData = SearchService::getAirlineLocationInfo($quotes);

                if (isset($quotes['results'])) {
                    $quotes = $flightQuoteSearchForm->applyFilters($quotes);
                }

                $params['lead_id'] = $lead->id;

                $dataProvider = new ArrayDataProvider([
                    'allModels' => $quotes['results'] ?? [],
                    'pagination' => [
                        'pageSize' => 10,
                        'params' => array_merge(Yii::$app->request->get(), $flightQuoteSearchForm->getFilters(), $params ?? []),
                    ],
                ]);

                $viewModel->quotes = $quotes;
                $viewModel->dataProvider = $dataProvider ?? new ArrayDataProvider();
                $viewModel->flightQuoteSearchForm = $flightQuoteSearchForm;
                $viewModel->keyCache = $keyCache;
                $viewModel->searchServiceDto = $searchServiceDto;
                $viewModel->itineraryForm = $itineraryForm;
                $viewModel->locations = $viewData['locations'] ?? [];
                $viewModel->airlines = $viewData['airlines'] ?? [];
            } catch (\Throwable $e) {
                Yii::$app->getSession()->addFlash('danger', $e->getMessage());
            }

            $viewModel->lead = $lead;
        } catch (\RuntimeException | \DomainException | NotFoundException | ForbiddenHttpException $e) {
            Yii::$app->getSession()->addFlash('warning', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'QuoteController::actionAjaxSearchQuotesByChat::Throwable');
            Yii::$app->getSession()->addFlash('danger', 'Internal Server Error');
        }

        if ($viewModel->flightRequestFormMode !== 'view') {
            $viewModel->itineraryForm->setEditMode();
        }

        return $this->renderAjax('_search_quotes_by_chat', [
            'viewModel' => $viewModel
        ]);
    }

    public function actionViewEditForm(): string
    {
        $id = Yii::$app->request->get('id');
        $chatId = Yii::$app->request->get('chat_id');
        $mode = Yii::$app->request->get('mode');

        /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $lead = $this->findLead($id);

        if (!Yii::$app->user->can('updateLead', ['lead' => $lead])) {
            throw new ForbiddenHttpException();
        }

        $form = new ItineraryEditForm($lead);
        if ($mode !== 'view') {
            $form->setEditMode();
        }
        return $this->renderAjax('partial/_flight_request_form', ['itineraryForm' => $form, 'chatId' => $chatId]);
    }

    public function actionEdit(): string
    {
        $id = Yii::$app->request->post('id');
        $chatId = Yii::$app->request->post('chat_id');
        $lead = $this->findLead($id);

        /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        if (!$chat = ClientChat::findOne((int)$chatId)) {
            throw new ForbiddenHttpException('Chat not found');
        }
        $refreshQuoteSearchResultPjax = false;
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ItineraryEditForm',
            ['segments' => 'SegmentEditForm']
        );
        $form = new ItineraryEditForm($lead, count($data['post']['SegmentEditForm']));

        if ($form->load($data['post']) && $form->validate()) {
            try {
                $keyCache = 'quote-search-' . $chat->cch_rid;
                $this->leadManageService->editItinerary($lead, $form);
                Yii::$app->cacheFile->delete($keyCache);
                Yii::$app->cacheFile->delete($keyCache . '-lead-' . $lead->id);
                Yii::$app->session->setFlash('success', 'Segments save.');
                $refreshQuoteSearchResultPjax = true;
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $lead = $this->findLead($id);
        $form = new ItineraryEditForm($lead);
        $form->setViewMode();
        return $this->renderPartial('partial/_flight_request_form', ['itineraryForm' => $form, 'chatId' => $chat->cch_id, 'refreshQuoteSearchResultPjax' => $refreshQuoteSearchResultPjax]);
    }

    public function actionValidate(): array
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);

        /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ItineraryEditForm',
            ['segments' => 'SegmentEditForm']
        );
        $form = new ItineraryEditForm($lead, count($data['post']['SegmentEditForm']));
        $form->load($data['post']);
        return CompositeFormHelper::ajaxValidate($form, $data['keys']);
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

        /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $chatId = Yii::$app->request->post('chatId');

        $lead = Lead::findOne(['id' => $leadId]);
        $chat = ClientChat::findOne((int)$chatId);
        if (Yii::$app->request->isPost) {
            //$gds = Yii::$app->request->post('gds');
            $key = Yii::$app->request->post('key');
            $keyCache = Yii::$app->request->post('keyCache', '');
            $providerProjectId = Yii::$app->request->post('projectId');

            if ($key && $lead && $chat) {
                if ((int) $providerProjectId === (int) $lead->project_id) {
                    $providerProjectId = null;
                }

                $keyCache = empty($keyCache) ? ('quote-search-' . $chat->cch_rid . '-lead-' . $lead->id) : $keyCache;
                $resultSearch = Yii::$app->cacheFile->get($keyCache);

                if ($resultSearch !== false) {
                    try {
                        $this->createQuote($keyCache, $resultSearch, $key, $lead, $chat, $providerProjectId, Auth::user());

                        $result['status'] = true;
                    } catch (\RuntimeException | \DomainException $e) {
                        $result['error'] = $e->getMessage();
                    } catch (\Throwable $e) {
                        $result['error'] = 'Internal Server Error';
                        Yii::error(AppHelper::throwableLog($e, true), 'ClientChatFlightController::actionCreateQuoteFromSearch::Throwable');
                    }
                } else {
                    $result['error'] = 'Not found Quote from Search result from Cache. Please update search request!';
                }
            } else {
                $result['error'] = 'Key or Lead or Chat is empty!';
            }
        }

        return $result;
    }

    /**
     * @param integer $id
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLead($id): Lead
    {
        if (($model = Lead::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function createLeadAndLinkWithChat(LeadCreateByChatForm $form, ClientChat $chat, int $userId): Lead
    {
        $leadManageService = Yii::createObject(\sales\model\lead\useCases\lead\create\LeadManageService::class);
        $lead = $leadManageService->createByClientChat($form, $chat, $userId);

        $leadUserConversion = LeadUserConversion::create(
            $lead->id,
            $userId,
            LeadUserConversionDictionary::DESCRIPTION_CLIENT_CHAT_MANUAL
        );
        (new LeadUserConversionRepository())->save($leadUserConversion);

        return $lead;
    }

    private function createQuote(string $keyCache, array $resultSearch, string $key, Lead $lead, ClientChat $chat, ?int $providerProjectId, Employee $user): Quote
    {
        $result = $keyCache ? $resultSearch['results'] : $resultSearch['data']['results'];
        foreach ($result as $entry) {
            if ($entry['key'] == $key) {
                $transaction = Quote::getDb()->beginTransaction();

                $quote = new Quote();
                $quote->uid = uniqid();
                $quote->lead_id = $lead->id;
                $quote->cabin = $lead->cabin;
                $quote->trip_type = $lead->trip_type;
                $quote->check_payment = ArrayHelper::getValue($entry, 'prices.isCk', true);
                $quote->fare_type = $entry['fareType'];
                $quote->gds = $entry['gds'];
                $quote->pcc = $entry['pcc'];
                $quote->main_airline_code = $entry['validatingCarrier'];
                $quote->last_ticket_date = $entry['prices']['lastTicketDate'];
                $quote->reservation_dump = str_replace('&nbsp;', ' ', SearchService::getItineraryDump($entry));
                $quote->employee_id = $user->id;
                $quote->employee_name = $user->username;
                $quote->origin_search_data = json_encode($entry);
                $quote->gds_offer_id = $entry['gdsOfferId'] ?? null;
                $quote->provider_project_id = $providerProjectId;
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
                    Yii::error(VarDumper::dumpAsString($quote->getErrors()), 'QuoteController:create-quote-from-search:quote:save');
                    $transaction->rollBack();
                    throw new \RuntimeException(VarDumper::dumpAsString($quote->errors));
                }

                if (isset($entry['trips']) && is_array($entry['trips'])) {
                    $ticketSegments = $quote->getTicketSegments();

                    foreach ($entry['trips'] as $tripKey => $tripEntry) {
                        $tripNr = $tripKey + 1;
                        $segmentNr = 1;

                        $trip = new QuoteTrip();
                        $trip->qt_duration = $tripEntry['duration'];

                        if (!$trip->validate()) {
                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($trip->getErrors()), 'QuoteController:create-quote-from-search:trip:save');
                            $transaction->rollBack();
                            throw new \RuntimeException(VarDumper::dumpAsString($trip->errors));
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
                                $segment->qs_cabin_basic = !empty($segmentEntry['cabinIsBasic']) ? 1 : 0;

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
                                    Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($segment->getErrors()), 'QuoteController:create-quote-from-search:segment:save');
                                    $transaction->rollBack();
                                    throw new \RuntimeException(VarDumper::dumpAsString($segment->errors));
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
                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($stop->getErrors()), 'QuoteController:create-quote-from-search:stop:save');
                                            $transaction->rollBack();
                                            throw new \RuntimeException(VarDumper::dumpAsString($stop->errors));
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
                                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($baggage->getErrors()), 'QuoteController:create-quote-from-search:baggage:save');
                                            $transaction->rollBack();
                                            throw new \RuntimeException(VarDumper::dumpAsString($baggage->errors));
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
                                                    Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($baggageCharge->getErrors()), 'QuoteController:create-quote-from-search:baggage_charge:save');
                                                    $transaction->rollBack();
                                                    throw new \RuntimeException(VarDumper::dumpAsString($baggageCharge->errors));
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
                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($trip->getErrors()), 'QuoteController:create-quote-from-search:trip:savekey');
                            $transaction->rollBack();
                            throw new \RuntimeException(VarDumper::dumpAsString($trip->errors));
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
                            Yii::error(VarDumper::dumpAsString($entry) . "\n" . VarDumper::dumpAsString($price->getErrors()), 'QuoteController:create-quote-from-search:price:save');
                            $transaction->rollBack();
                            throw new \RuntimeException(VarDumper::dumpAsString($price->errors));
                        }

                        $quote->link('quotePrices', $price);
                    }
                }

                if ($lead->called_expert) {
                    $quote->sendUpdateBO();
                }

                try {
                    $capture = $this->generateQuoteCapture($quote);

                    $this->sendCapturesQuote($chat, $quote, $capture);
                } catch (\Throwable $e) {
                    Yii::error(AppHelper::throwableLog($e, true), 'ClientChatFlightQuoteController:generateQuoteCapture');
                    $transaction->rollBack();
                    throw new $e();
                }

                ClientChatSocketCommands::clientChatAddQuotesButton($chat, $lead->id);

                try {
                    QuoteLabelService::processingQuoteLabel($entry, $quote->id);
                } catch (\Throwable $throwable) {
                    \Yii::warning($throwable->getMessage(), 'ClientChatFlightQuoteController:actionCreateQuoteFromSearch:QuoteLabel');
                }

                $transaction->commit();
                return $quote;
            }
        }
        throw new \RuntimeException('Not found quote in cache search result by key: ' . $key);
    }

    private function generateQuoteCapture(Quote $quote): array
    {
        $communication = Yii::$app->communication;

        $project = $quote->lead->project;
        $projectContactInfo = [];

        if ($project && $project->contact_info) {
            $projectContactInfo = @json_decode($project->contact_info, true);
        }

        $content_data = $quote->lead->getEmailData2([$quote->id], $projectContactInfo);
        if (isset($content_data['quotes'])) {
            if (count($content_data['quotes']) > 1) {
                throw new \DomainException('Count quotes > 1');
            }
//            if (isset($content_data['quotes'][0])) {
//                $tmp = $content_data['quotes'][0];
//                unset($content_data['quotes']);
//                $content_data['quote'] = $tmp;
//            }
        } else {
            throw new \DomainException('Not found quote');
        }

        $mailCapture = $communication->mailCapture(
            $quote->lead->project_id,
            'chat_offer',
            '',
            '',
            $content_data,
            Yii::$app->language ?: 'en-US',
            [
                'img_width' => 265,
                'img_height' => 60,
                'img_format' => 'png',
                'img_update' => 1,
            ]
        );

        if (!isset($mailCapture['data']['img'])) {
            throw new \RuntimeException('Create capture error.');
        }

        return [
            'img' => $mailCapture['data']['img'],
            'checkoutUrl' => $quote->getCheckoutUrlPage(),
        ];
    }

    private function sendCapturesQuote(ClientChat $chat, Quote $quote, array $capture): void
    {
        $message = [
            'rid' => $chat->cch_rid,
            'attachments' => [
                [
                    'image_url' => $capture['img'],
                    'actions' => [
                        [
                            'type' => 'web_url',
                            'text' => 'Offer',
                            'msg' => $capture['checkoutUrl'],
                        ],
                    ],
                ]
            ]
        ];

        if (($rocketUserId = UserClientChatDataService::getCurrentRcUserId()) && ($rocketToken = UserClientChatDataService::getCurrentAuthToken())) {
            $headers = [
                'X-User-Id' => $rocketUserId,
                'X-Auth-Token' => $rocketToken,
            ];
        } else {
            $headers = Yii::$app->rchat->getSystemAuthDataHeader();
        }

        $result = Yii::$app->chatBot->sendOffer($message, $headers);
        if (!empty($result['error']['message'])) {
            throw new \RuntimeException($result['error']['message']);
        }

        $quote->setStatusSend();
        if (!$this->quoteRepository->save($quote)) {
            throw new \RuntimeException(VarDumper::dumpAsString($quote));
        }
    }
}
