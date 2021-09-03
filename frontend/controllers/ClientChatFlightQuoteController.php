<?php

namespace frontend\controllers;

use common\components\SearchService;
use common\models\Lead;
use frontend\helpers\QuoteHelper;
use sales\auth\Auth;
use sales\dto\searchService\SearchServiceQuoteDTO;
use sales\forms\api\searchQuote\FlightQuoteSearchForm;
use sales\forms\CompositeFormHelper;
use sales\forms\lead\ItineraryEditForm;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatDataRequest\entity\ClientChatDataRequest;
use sales\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use sales\model\lead\useCases\lead\create\LeadCreateByChatForm;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\leadUserConversion\repository\LeadUserConversionRepository;
use sales\model\leadUserConversion\service\LeadUserConversionDictionary;
use sales\repositories\NotFoundException;
use sales\services\lead\LeadManageService;
use sales\viewModel\chat\ViewModelSearchQuotes;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
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
 */
class ClientChatFlightQuoteController extends FController
{
    private LeadManageService $leadManageService;

    public function __construct($id, $module, LeadManageService $leadManageService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadManageService = $leadManageService;
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
                    'validate'
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
                $quotes = Yii::$app->cacheFile->get($keyCache);
                if ($quotes === false) {
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
        } catch (\RuntimeException | \DomainException | NotFoundException $e) {
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

        if (!Yii::$app->user->can('updateLead', ['lead' => $lead])) {
            throw new ForbiddenHttpException();
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

        if (!Yii::$app->user->can('updateLead', ['lead' => $lead])) {
            throw new ForbiddenHttpException();
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
}
