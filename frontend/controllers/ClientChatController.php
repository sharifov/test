<?php

namespace frontend\controllers;

use common\components\CentrifugoService;
use common\models\Client;
use common\models\Department;
use common\models\Lead;
use common\models\Notifications;
use common\models\Project;
use common\models\Quote;
use common\models\search\LeadSearch;
use common\models\UserConnection;
use common\models\VisitorLog;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\clientChat\ClientChatAccessWidget;
use frontend\widgets\notification\NotificationMessage;
use frontend\widgets\notification\NotificationSocketWidget;
use frontend\widgets\notification\NotificationWidget;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSearch;
use sales\entities\chat\ChatExtendedGraphsSearch;
use sales\entities\chat\ChatFeedbackGraphSearch;
use sales\entities\chat\ChatGraphsSearch;
use sales\forms\clientChat\RealTimeStartChatForm;
use sales\helpers\app\AppHelper;
use sales\helpers\app\AppParamsHelper;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\dashboard\ReadUnreadFilter;
use sales\model\clientChat\dashboard\GroupFilter;
use sales\model\clientChat\entity\search\ClientChatSearch;
use sales\model\clientChat\useCase\close\ClientChatCloseForm;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\sendOffer\GenerateImagesForm;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatNote\ClientChatNoteRepository;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestRepository;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use sales\repositories\project\ProjectRepository;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use sales\services\TransactionManager;
use sales\viewModel\chat\ViewModelChatExtendedGraph;
use sales\viewModel\chat\ViewModelChatFeedbackGraph;
use sales\viewModel\chat\ViewModelChatGraph;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ClientChatController
 *
 * @package frontend\controllers
 *
 * @property ClientChatRepository           $clientChatRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatMessageService       $clientChatMessageService
 * @property ClientChatService              $clientChatService
 * @property ClientChatUserAccessService    $clientChatUserAccessService
 * @property ClientChatNoteRepository       $clientChatNoteRepository
 * @property LeadRepository                 $leadRepository
 * @property TransactionManager             $transactionManager
 * @property ClientChatChannelRepository    $clientChatChannelRepository
 * @property ProjectRepository              $projectRepository
 * @property ClientChatRequestService       $clientChatRequestService
 * @property ClientChatRequestRepository    $clientChatRequestRepository
 */
class ClientChatController extends FController
{
    private const CLIENT_CHAT_PAGE_SIZE = 10;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var ClientChatUserAccessRepository
     */
    private ClientChatUserAccessRepository $clientChatUserAccessRepository;
    /**
     * @var ClientChatMessageService
     */
    private ClientChatMessageService $clientChatMessageService;
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var ClientChatUserAccessService
     */
    private ClientChatUserAccessService $clientChatUserAccessService;

    private ClientChatNoteRepository $clientChatNoteRepository;
    /**
     * @var LeadRepository
     */
    private $leadRepository;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;
    /**
     * @var ClientChatChannelRepository
     */
    private ClientChatChannelRepository $clientChatChannelRepository;
    /**
     * @var ProjectRepository
     */
    private ProjectRepository $projectRepository;
    /**
     * @var ClientChatRequestService
     */
    private ClientChatRequestService $clientChatRequestService;
    /**
     * @var ClientChatRequestRepository
     */
    private ClientChatRequestRepository $clientChatRequestRepository;

    public function __construct(
        $id,
        $module,
        ClientChatRepository $clientChatRepository,
        ClientChatUserAccessRepository $clientChatUserAccessRepository,
        ClientChatMessageService $clientChatMessageService,
        ClientChatService $clientChatService,
        ClientChatUserAccessService $clientChatUserAccessService,
        ClientChatNoteRepository $clientChatNoteRepository,
        LeadRepository $leadRepository,
        TransactionManager $transactionManager,
        ClientChatChannelRepository $clientChatChannelRepository,
        ProjectRepository $projectRepository,
        ClientChatRequestService $clientChatRequestService,
        ClientChatRequestRepository $clientChatRequestRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatService = $clientChatService;
        $this->clientChatUserAccessService = $clientChatUserAccessService;
        $this->clientChatNoteRepository = $clientChatNoteRepository;
        $this->leadRepository = $leadRepository;
        $this->transactionManager = $transactionManager;
        $this->clientChatChannelRepository = $clientChatChannelRepository;
        $this->projectRepository = $projectRepository;
        $this->clientChatRequestService = $clientChatRequestService;
        $this->clientChatRequestRepository = $clientChatRequestRepository;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionIndex()
    {
        $userId = Auth::id();

        $channels = ClientChatChannel::find()->select(['ccc_name', 'ccc_id'])->joinWithCcuc($userId)->indexBy('ccc_id')->column();

        $filter = new FilterForm($channels);

        if (!$filter->load(Yii::$app->request->get()) || !$filter->validate()) {
            $filter->loadDefaultValues();
        }

        $filter->loadDefaultValuesByPermissions();

        //todo remove after added logic free to take
        if (GroupFilter::isFreeToTake($filter->group)) {
            $filter->group = GroupFilter::MY;
        }

        $page = (int) \Yii::$app->request->get('page');
        if ($page < 1) {
            $page = 1;
        }

        $dataProvider = null;
        /** @var $channels ClientChatChannel[] */
        if ($channels) {
            $dataProvider = (new ClientChatSearch())->getListOfChats(Auth::user(), array_keys($channels), $filter);
        }
        
        $clientChat = null;
        $chid = (int) Yii::$app->request->get('chid');

        if ($chid) {
            try {
                $clientChat = $this->clientChatRepository->findById($chid);

                if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
                    throw new ForbiddenHttpException('You do not have access to this chat', 403);
                }

                if ($clientChat->cch_owner_user_id && $clientChat->isOwner(Auth::id())) {
                    $this->clientChatMessageService->discardUnreadMessages($clientChat->cch_id, $clientChat->cch_owner_user_id);
                    if ($dataProvider && ($models = $dataProvider->getModels())) {
                        if (isset($models[$clientChat->cch_id])) {
                            $models[$clientChat->cch_id]['count_unread_messages'] = $this->clientChatMessageService->getCountOfChatUnreadMessagesByUser($clientChat->cch_id, Auth::id());
                        }
                        $dataProvider->setModels($models);
                        $dataProvider->refresh();
                    }
                }

                if ($clientChat->isClosed()) {
                    $history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
                }
            } catch (NotFoundException $e) {
                $clientChat = null;
            } catch (\DomainException $e) {
                $clientChat = null;
            }
        }


        $loadingChannels = \Yii::$app->request->get('loadingChannels');
        if ($dataProvider) {
            if ($loadingChannels) {
                $dataProvider->pagination->setPage($page - 1);
//            if (\Yii::$app->request->post('loadingChannels')) {
//                $dataProvider->pagination->page = $filter->page;
//            } else {
//                $dataProvider->pagination->page = $filter->page = 0;
//            }

                $response = [
                    'html' => '',
                    'page' => $page,
                ];

                if ($dataProvider->getCount()) {
                    $response['html'] = $this->renderPartial('partial/_client-chat-item', [
                        'clientChats' => $dataProvider->getModels(),
                        'clientChatId' => $clientChat ? $clientChat->cch_id : '',
                    ]);
                    $response['page'] = $page + 1;
                }

                return $this->asJson($response);
            } else {
                if ($page > 1) {
                    $dataProvider->pagination->setPage(0);
                    $dataProvider->pagination->pageSize = $page * $dataProvider->pagination->pageSize;
                } else {
                    $dataProvider->pagination->setPage($page - 1);
                }
            }
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'clientChat' => $clientChat,
            'client' => $clientChat->cchClient ?? null,
            'history' => $history ?? null,
            'filter' => $filter,
            'page' => $page + 1,
        ]);
    }

    public function actionInfo()
    {
        $cchId = \Yii::$app->request->post('cch_id');

        $result = [
            'html' => '',
            'message' => '',
        ];
        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
            if ($clientChat->cch_owner_user_id && $clientChat->isOwner(Auth::id())) {
                $this->clientChatMessageService->discardUnreadMessages($clientChat->cch_id, (int)$clientChat->cch_owner_user_id);
            }

            $result['html'] = $this->renderPartial('partial/_client-chat-info', [
                'clientChat' => $clientChat,
                'client' => $clientChat->cchClient,
            ]);
            $result['noteHtml'] = $this->renderAjax('partial/_client-chat-note', [
                'clientChat' => $clientChat,
                'model' => new ClientChatNote(),
            ]);
        } catch (NotFoundException $e) {
            $result['message'] = $e->getMessage();
        }

        return $this->asJson($result);
    }

    public function actionNote(): Response
    {
        $cchId = \Yii::$app->request->post('cch_id');

        $result = [
            'html' => '',
            'message' => '',
        ];
        try {
            $clientChat = $this->clientChatRepository->findById($cchId);

            $result['html'] = $this->renderPartial('partial/_client-chat-note', [
                'clientChat' => $clientChat,
                'model' => new ClientChatNote(),
            ]);
        } catch (NotFoundException $e) {
        }

        return $this->asJson($result);
    }

    public function actionCreateNote(): string
    {
        $cchId = Yii::$app->request->get('cch_id');
        $model = new ClientChatNote();
        $model->ccn_user_id = Auth::id();

        $clientChat = $this->clientChatRepository->findById($cchId);

        if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            try {
                $this->clientChatNoteRepository->save($model);
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'ClientChatController:actionCreateNote:save'
                );
            }
        }

        return $this->renderAjax('partial/_client-chat-note', [
            'clientChat' => $clientChat,
            'model' => $model,
            'showContent' => false,
        ]);
    }

    public function actionDeleteNote(): string
    {
        try {
            $cchId = Yii::$app->request->get('cch_id');
            $ccnId = Yii::$app->request->get('ccn_id');

            $clientChat = $this->clientChatRepository->findById($cchId);

            if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
                throw new ForbiddenHttpException('You do not have access to perform this action', 403);
            }

            if ($clientChatNote = $this->clientChatNoteRepository->findById($ccnId)) {
                $this->clientChatNoteRepository->toggleDeleted($clientChatNote);
            }
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'ClientChatController:actionDeleteNote:delete'
            );
        }

        return $this->renderAjax('partial/_client-chat-note', [
            'clientChat' => $clientChat ?? null,
            'model' => new ClientChatNote(),
            'showContent' => true,
        ]);
    }

    public function actionAccessManage(): \yii\web\Response
    {
        $ccuaId = \Yii::$app->request->post('ccuaId');
        $accessAction = \Yii::$app->request->post('accessAction');

        try {
            $result = [
                'success' => false,
                'notifyMessage' => '',
                'notifyTitle' => '',
                'notifyType' => '',
            ];

            $ccua = $this->clientChatUserAccessRepository->findByPrimaryKey($ccuaId);
            $clientChat = $this->clientChatRepository->findById($ccua->ccua_cch_id);
            $this->clientChatUserAccessService->updateStatus($clientChat, $ccua, (int) $accessAction);

            $result['success'] = true;
        } catch (\RuntimeException | \DomainException | NotFoundException $e) {
            $result['notifyMessage'] = $e->getMessage();
            if (ClientChatCodeException::isWarningMessage($e)) {
                $result['notifyTitle'] = 'Warning';
                $result['notifyType'] = 'warning';
            } else {
                $result['notifyTitle'] = 'Error';
                $result['notifyType'] = 'error';
            }
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableFormatter($e), 'ClientChatController::actionAccessManage::Throwable');
            $result['notifyMessage'] = 'Internal Server Error';
            $result['notifyTitle'] = 'Error';
            $result['notifyType'] = 'error';
        }

        return $this->asJson($result);
    }

    public function actionAjaxDataInfo()
    {
        $cchId = \Yii::$app->request->post('cchId');

        $clientChat = null;
        $visitorLog = null;
        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
            if ($clientChat->ccv && $clientChat->ccv->ccv_cvd_id) {
                $visitorLog = VisitorLog::find()->byCvdId($clientChat->ccv->ccv_cvd_id)->orderBy(['vl_created_dt' => SORT_DESC])->one();
            }
        } catch (NotFoundException $e) {
        }

        $requestSearch = new ClientChatRequestSearch();
        $visitorId = '';
        if ($clientChat->ccv && $clientChat->ccv->ccvCvd) {
            $visitorId = $clientChat->ccv->ccvCvd->cvd_visitor_rc_id ?? '';
        }
        $data[$requestSearch->formName()]['ccr_visitor_id'] = $visitorId;
        $data[$requestSearch->formName()]['ccr_event'] = ClientChatRequest::EVENT_TRACK;
        $dataProviderRequest = $requestSearch->search($data);
        $dataProviderRequest->setPagination(['pageSize' => 40]);

        if ($clientChat->cchClient) {
            $leadSearch = new LeadSearch();
            $data[$leadSearch->formName()]['client_id'] = $clientChat->cchClient->id;
            $data[$leadSearch->formName()]['project_id'] = $clientChat->cch_project_id;
            $leadDataProvider = $leadSearch->search($data);

            $casesSearch = new CasesSearch();
            $data[$casesSearch->formName()]['cs_client_id'] = $clientChat->cchClient->id;
            $data[$casesSearch->formName()]['cs_project_id'] = $clientChat->cch_project_id;
            $casesDataProvider = $casesSearch->search($data, Auth::user());
        }

        return $this->renderAjax('partial/_data_info', [
            'clientChat' => $clientChat,
            'clientChatVisitorData' => $clientChat->ccv->ccvCvd ?? null,
            'visitorLog' => $visitorLog,
            'dataProviderRequest' => $dataProviderRequest,
            'client' => $clientChat->cchClient ?? null,
            'leadDataProvider' => $leadDataProvider ?? null,
            'casesDataProvider' => $casesDataProvider ?? null,
        ]);
    }

    public function actionStats()
    {
        $model = new ChatGraphsSearch();
        $model->load(\Yii::$app->request->queryParams);

        return $this->render('stats', ['model' => $model]);
    }

    public function actionAjaxGetChartStats(): \yii\web\Response
    {
        $statsSearch = new ChatGraphsSearch();
        $statsSearch->load(Yii::$app->request->post());
        if ($statsSearch->validate()) {
            $html = $this->renderAjax('partial/_stats_chart', [
                'viewModel' => new ViewModelChatGraph($statsSearch->stats(), $statsSearch),
            ]);
        }

        $response = [
            'html' => $html ?? '',
            'error' => $statsSearch->hasErrors(),
            'message' => $statsSearch->getErrorSummary(true),
        ];

        return $this->asJson($response);
    }

    public function actionExtendedStats()
    {
        $model = new ChatExtendedGraphsSearch();
        $model->load(\Yii::$app->request->queryParams);

        return $this->render('extended-stats', ['model' => $model]);
    }

    public function actionAjaxGetExtendedStatsChart(): \yii\web\Response
    {
        $statsSearch = new ChatExtendedGraphsSearch();
        $statsSearch->load(Yii::$app->request->post());
        if ($statsSearch->validate()) {
            $html = $this->renderAjax('partial/_extended_stats_chart', [
                'viewModel' => new ViewModelChatExtendedGraph($statsSearch->stats(), $statsSearch),
            ]);
        }

        $response = [
            'html' => $html ?? '',
            'error' => $statsSearch->hasErrors(),
            'message' => $statsSearch->getErrorSummary(true),
        ];

        return $this->asJson($response);
    }

    public function actionFeedbackStats()
    {
        $model = new ChatFeedbackGraphSearch();
        $model->load(\Yii::$app->request->queryParams);

        return $this->render('feedback-stats', ['model' => $model]);
    }

    public function actionAjaxGetFeedbackStatsChart(): \yii\web\Response
    {
        $statsSearch = new ChatFeedbackGraphSearch();
        $statsSearch->load(Yii::$app->request->post());
        if ($statsSearch->validate()) {
            $html = $this->renderAjax('partial/_feedback_stats_chart', [
                'viewModel' => new ViewModelChatFeedbackGraph($statsSearch->stats(), $statsSearch),
            ]);
        }

        $response = [
            'html' => $html ?? '',
            'error' => $statsSearch->hasErrors(),
            'message' => $statsSearch->getErrorSummary(true),
        ];

        return $this->asJson($response);
    }

    public function actionReport()
    {
        $searchModel = new ClientChatSearch();
        $dataProvider = $searchModel->report(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxClose()
    {
        $cchId = \Yii::$app->request->post('cchId');

        $form = new ClientChatCloseForm();
        $form->cchId = $cchId;

        try {
            $form->load(Yii::$app->request->post());

            $chat = $this->clientChatRepository->findById((int) $form->cchId);

            if (!Auth::can('client-chat/manage/all', ['chat' => $chat])) {
                throw new ForbiddenHttpException('You do not have access to manage this chat', 403);
            }

            if (Yii::$app->request->isPjax && $form->validate()) {
                $this->clientChatService->closeConversation($form, Auth::user());

                return '<script>$("#modal-sm").modal("hide"); refreshChatPage(' . $form->cchId . ', ' . ClientChat::TAB_ARCHIVE . '); createNotify("Success", "Room successfully closed", "success")</script>';
            }
        } catch (NotFoundException | ForbiddenHttpException $e) {
            return '<script>setTimeout(function () {$("#modal-sm").modal("hide");}, 500); createNotify("Error", "' . $e->getMessage() . '", "error")</script>';
        } catch (\RuntimeException $e) {
            $form->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
			Yii::error(VarDumper::dumpAsString(AppHelper::throwableLog($e, true)), 'ClientChatController::actionAjaxClose::Throwable');
            $form->addError('general', 'Internal Server Error');
        }

        return $this->renderAjax('partial/_close_chat_view', [
            'cchId' => $cchId,
            'closeForm' => $form,
        ]);
    }

    public function actionAjaxHistory()
    {
        $chatId = \Yii::$app->request->post('cchId');

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            //			if ($clientChat->isClosed()) {
//				$history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
//			}
        } catch (NotFoundException $e) {
            $clientChat = null;
        }

        return $this->renderAjax('partial/_chat_history', [
            //			'history' => $history ?? null,
            'clientChat' => $clientChat,
        ]);
    }

    public function actionAjaxTransferView(): string
    {
        $cchId = Yii::$app->request->post('cchId');

        $clientChat = ClientChat::findOne($cchId);

        $form = new ClientChatTransferForm();

        if ($clientChat) {
            $form->cchId = $clientChat->cch_id;
            $form->depId = $clientChat->cchChannel->ccc_dep_id;
            $form->isOnline = $clientChat->cch_client_online;
        }

        if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        try {
            if ($form->load(Yii::$app->request->post()) && !$form->pjaxReload && $form->validate()) {
                $newDepartment = $this->clientChatService->transfer($form, Auth::user());

                return '<script>
                        $("#modal-sm").modal("hide"); 
                        refreshChatPage(' . $form->cchId . '); 
                        createNotify("Success", "Chat successfully transferred to ' . $newDepartment->dep_name . ' department. ", "success");
                    </script>';
            }

            if ($form->pjaxReload) {
                $form->pjaxReload = 0;
                $form->agentId = null;
            }
        } catch (\DomainException $e) {
            $form->addError('general', $e->getMessage());
        } catch (\RuntimeException $e) {
            $form->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
            $form->addError('general', 'Internal Server Error');
            Yii::error(AppHelper::throwableFormatter($e), 'ClientChatController::actionAjaxTransferView::Throwable');
        }

        return $this->renderAjax('partial/_transfer_view', ['clientChat' => $clientChat, 'transferForm' => $form]);
    }

    public function actionPjaxUpdateChatWidget()
    {
        $widget = ClientChatAccessWidget::getInstance();
        $widget->userId = Auth::id();

        return $widget->run();
    }

    public function actionRefreshNotification(): string
    {
        if (Yii::$app->params['settings']['notification_web_socket']) {
            $widget = new NotificationSocketWidget();
        } else {
            $widget = new NotificationWidget();
        }
        $widget->userId = Auth::id();

        return $widget->run();
    }

    public function actionDiscardUnreadMessages(): void
    {
        $chatId = (int)Yii::$app->request->post('cchId');
        $chat = ClientChat::findOne($chatId);
        if (!$chat) {
            return;
        }
        $userId = Auth::id();
        if ($chat->cch_owner_user_id && $chat->isOwner($userId)) {
            $this->clientChatMessageService->discardUnreadMessages($chatId, $userId);
        }
    }

    public function actionSendOfferList(): string
    {
        $chatId = (int) \Yii::$app->request->post('chat_id');
        $leadId = (int) \Yii::$app->request->post('lead_id');
        $errorMessage = '';
        $dataProvider = null;

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
                throw new ForbiddenHttpException('You do not have access to perform this action', 403);
            }
            $lead = $this->leadRepository->find($leadId);

            if (!$this->sendOfferCheckAccess($clientChat, Auth::user())) {
                throw new \DomainException('Access denied.');
            }

            if (!$clientChat->isAssignedLead($lead->id)) {
                throw new \DomainException('Lead is not assigned to Client Chat');
            }

            if (!$lead->isExistQuotesForSend()) {
                throw new \DomainException('Not found Quote for Send');
            }

            $dataProvider = $this->getSendOfferProvider($lead);
        } catch (\DomainException $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->renderAjax('partial/_send_offer_list', [
            'dataProvider' => $dataProvider,
            'errorMessage' => $errorMessage,
            'chatId' => $chatId,
            'leadId' => $leadId,
        ]);
    }

    public function actionSendOfferGenerate(): string
    {
        $errorMessage = '';
        $captures = [];

        $form = new GenerateImagesForm();

        if (!$form->load(Yii::$app->request->post())) {
            return $this->renderAjax('partial/_send_offer_generate', [
                'errorMessage' => 'Cant load Data',
                'form' => $form,
                'captures' => $captures,
            ]);
        }

        if (!$form->validate()) {
            return $this->renderAjax('partial/_send_offer_generate', [
                'errorMessage' => '',
                'form' => $form,
                'captures' => $captures,
            ]);
        }

        try {
            if (!$this->sendOfferCheckAccess($form->chat, Auth::user())) {
                throw new \DomainException('Access denied.');
            }
            foreach ($form->quotes as $quote) {
                if ($capture = $this->generateQuoteCapture($quote)) {
                    /** @var Quote $quote */
                    $data = $quote->getPricesData();
                    $selling = $data['total']['selling'] ?? 0;
                    if (($pos = strpos($selling, '.')) > 0) {
                        $str = substr($selling, $pos + 1, 2);
                        if ($str == '') {
                            $selling .= '00';
                        } elseif (strlen($str) === 1) {
                            $selling .= '0';
                        }
                    } else {
                        $selling .= '.00';
                    }
                    $price = (int) (str_replace('.', '', $selling)) * 100;
                    $captures[] = [
                        'price' => $price,
                        'data' => $capture,
                    ];
                }
            }
            if (!$captures) {
                throw new \DomainException('Not generated captures. Try again.');
            }

            usort($captures, static function ($a, $b) {
                if ($a['price'] === $b['price']) {
                    return 0;
                }

                return ($a['price'] < $b['price']) ? -1 : 1;
            });

            if (!$this->saveQuoteCaptures($captures, Auth::id(), $form->chatId, $form->leadId)) {
                throw new \DomainException('Cant tmp save quotes. Please try again later.');
            }
        } catch (\DomainException $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->renderAjax('partial/_send_offer_generate', [
            'errorMessage' => $errorMessage,
            'form' => $form,
            'captures' => ArrayHelper::getColumn($captures, 'data'),
        ]);
    }

    public function actionSendOffer(): Response
    {
        $out = ['error' => false, 'message' => ''];
        $chatId = (int) \Yii::$app->request->post('chatId');
        $leadId = (int) \Yii::$app->request->post('leadId');

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            $lead = $this->leadRepository->find($leadId);

            if (!$captures = $this->getQuoteCaptures(Auth::id(), $clientChat->cch_id, $lead->id)) {
                throw new \DomainException('Not found saved quote captures. Please try again.');
            }

            $message = $this->createOfferMessage($clientChat, ArrayHelper::getColumn($captures, 'data'));

            if (($rocketUserId = Auth::user()->userProfile->up_rc_user_id) && ($rocketToken = Auth::user()->userProfile->up_rc_auth_token)) {
                $headers = [
                    'X-User-Id' => $rocketUserId,
                    'X-Auth-Token' => $rocketToken,
                ];
            } else {
                $headers = Yii::$app->rchat->getSystemAuthDataHeader();
            }

            Yii::$app->chatBot->sendMessage($message, $headers);
            $this->removeQuoteCaptures(Auth::id(), $clientChat->cch_id, $lead->id);
        } catch (\DomainException $e) {
            $out['error'] = true;
            $out['message'] = $e->getMessage();
        }

        return $this->asJson($out);
    }

    public function actionMoveOffer()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        $chatId = (int) Yii::$app->request->post('chatId');
        $leadId = (int) Yii::$app->request->post('leadId');
        $captureKey = (int) Yii::$app->request->post('captureKey');
        $type = (string) Yii::$app->request->post('type');

        if (!$chatId || !$leadId || !$type) {
            throw new BadRequestHttpException('Not found chatId or leadId or type');
        }

        if ($type !== 'up' && $type !== 'down') {
            throw new BadRequestHttpException('Type value is invalid');
        }

        if (!$captures = $this->getQuoteCaptures(Auth::id(), $chatId, $leadId)) {
            throw new BadRequestHttpException('Not found saved quote captures. Please try again.');
        }

        $originalCaptures = $captures;

        if (!isset($captures[$captureKey])) {
            throw new BadRequestHttpException('Not found capture with this key ' . $captureKey);
        }

        if ($type === 'up') {
            $newCaptureKey = $captureKey - 1;
        } else {
            $newCaptureKey = $captureKey + 1;
        }

        if (!isset($captures[$newCaptureKey])) {
            throw new BadRequestHttpException('Move error. Not found capture with new Key ' . $newCaptureKey);
        }

        $tmpCapture = $captures[$captureKey];
        $captures[$captureKey] = $captures[$newCaptureKey];
        $captures[$newCaptureKey] = $tmpCapture;

        $form = new GenerateImagesForm();
        $form->leadId = $leadId;
        $form->chatId = $chatId;

        if (!$this->saveQuoteCaptures($captures, Auth::id(), $chatId, $leadId)) {
            return $this->asJson([
                'view' => $this->renderAjax('partial/_send_offer_generate', [
                    'errorMessage' => '',
                    'form' => $form,
                    'captures' => ArrayHelper::getColumn($originalCaptures, 'data'),
                ]),
                'error' => 'Cant tmp save quotes. Please try again later.',
            ]);
        }

        return $this->asJson([
            'view' => $this->renderAjax('partial/_send_offer_generate', [
                'errorMessage' => '',
                'form' => $form,
                'captures' => ArrayHelper::getColumn($captures, 'data'),
            ]),
            'error' => false,
        ]);
    }

    public function actionMonitor()
    {
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            $searchModel = new ClientChatSearch();
            $chatsData = $searchModel->searchRealtimeClientChatActivity($params);

            CentrifugoService::sendMsg(json_encode([
                'chatsData' => $chatsData,
            ]), 'realtimeClientChatChannel');
        } else {
            $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';

            return $this->render('monitor');
        }
    }

    public function actionRealTime()
    {
        $host = AppParamsHelper::liveChatRealTimeVisitorsUrl();
        $projectsWithKeys = Project::getListByUserWithProjectKeys(Auth::id());

        return $this->render('real-time', ['host' => $host, 'projectsWithKeys' => json_encode($projectsWithKeys, true)]);
    }

    public function actionAjaxCancelTransfer()
    {
        $cchId = Yii::$app->request->post('cchId');

        $result = [
            'error' => false,
            'message' => 'Transfer cancelled successfully',
        ];

        try {
            $chat = $this->clientChatRepository->findById($cchId);

            $this->transactionManager->wrap(function () use ($chat) {
                $this->clientChatUserAccessService->disableAccessForOtherUsersBatch($chat, $chat->cch_owner_user_id);
                $this->clientChatService->cancelTransfer($chat, Auth::user(), ClientChatStatusLog::ACTION_ACCEPT_TRANSFER);
            });
        } catch (\DomainException | \RuntimeException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'ClientChatController::actionAjaxCancelTransfer::Throwable');
            $result['error'] = true;
            $result['message'] = 'Internal Server Error';
        }

        return $this->asJson($result);
    }

    public function actionRealTimeStartChat(): string
    {
        $visitorId = Yii::$app->request->get('visitorId', '');
        $projectName = Yii::$app->request->get('projectName', '');
        $visitorName = Yii::$app->request->get('visitorName', '');

        $form = new RealTimeStartChatForm($visitorId, $projectName, $visitorName);

        try {
            if ($form->projectName) {
                $form->projectId = $this->projectRepository->getIdByProjectKey($form->projectName);
            }

            if (Yii::$app->request->isPjax && $form->load(Yii::$app->request->post()) && $form->validate()) {
                $this->clientChatService->createByAgent($form, Auth::id());

                return '<script>$("#modal-sm").modal("hide"); createNotify("Success", "Message was successfully sent to client", "success");</script>';
            }
        } catch (\RuntimeException | \DomainException $e) {
            $form->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'ClientChatController::actionRealTimeStartChat::Throwable');
            $form->addError('general', 'Internal Server Error');
        }

        $domainError = '';
        $channels = [];
        try {
            $channels = $this->clientChatChannelRepository->getByUserAndProject(Auth::id(), $form->projectId, Department::DEPARTMENT_EXCHANGE);
            $channels = ArrayHelper::map($channels, 'ccc_id', 'ccc_name');

            if (!$channels) {
                $domainError = 'You dont have access to channels';
            }
        } catch (NotFoundException $e) {
            $domainError = $e->getMessage();
        }

        return $this->renderAjax('partial/_real_time_start_chat', [
            'startChatForm' => $form,
            'channels' => $channels,
            'domainError' => $domainError,
        ]);
    }

    private function createOfferMessage(ClientChat $chat, array $captures): array
    {
        $attachments = [];

        foreach ($captures as $capture) {
            $attachments[] = [
                'image_url' => $capture['img'],
                'actions' => [
                    [
                        'type' => 'web_url',
                        'msg_in_chat_window' => true,
                        'text' => 'Offer',
                        'msg' => $capture['checkoutUrl'],
                    ],
                ],
                'fields' => [
                    [
                        'short' => true,
                        'title' => 'Offer',
                        'value' => '[' . $capture['checkoutUrl'] . '](' . $capture['checkoutUrl'] . ')',
                    ],
                ],
            ];
        }

        $data = [
            'message' => [
                'rid' => $chat->cch_rid,
                'attachments' => $attachments,
                'file' => [
                    'customTemplate' => 'carousel',
                ],
            ],
        ];

        return $data;
    }

    //todo
    private function sendOfferCheckAccess($chat, $user): bool
    {
        return true;
    }

    private function getSendOfferProvider(Lead $lead): ActiveDataProvider
    {
        return $lead->getQuotesProvider([], [Quote::STATUS_CREATED, Quote::STATUS_SEND, Quote::STATUS_OPENED]);
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

        try {
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
            $url = $mailCapture['data'];

            return [
                'img' => $url['host'] . $url['dir'] . $url['img'],
                'checkoutUrl' => $quote->getCheckoutUrlPage(),
            ];
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString([
                'error' => AppHelper::throwableFormatter($e),
                'quote' => $quote->getAttributes(),
            ]), 'ClientChatController:generateQuoteCapture');
        }

        return [];
    }

    private function saveQuoteCaptures(array $captures, int $userId, int $chatId, int $leadId): bool
    {
        return Yii::$app->cache->set($this->getQuoteCaptureCacheKey($userId, $chatId, $leadId), $captures, 600);
    }

    private function getQuoteCaptures(int $userId, int $chatId, int $leadId)
    {
        return Yii::$app->cache->get($this->getQuoteCaptureCacheKey($userId, $chatId, $leadId));
    }

    private function removeQuoteCaptures(int $userId, int $chatId, int $leadId): void
    {
        if (!Yii::$app->cache->delete($this->getQuoteCaptureCacheKey($userId, $chatId, $leadId))) {
            Yii::error(
                VarDumper::dumpAsString([
                    'message' => 'Cant remove tmp quotes captures',
                    'userId' => $userId,
                    'chatId' => $chatId,
                    'leadId' => $leadId,
                ]),
                'ClientChatController:removeQuoteCaptures'
            );
        }
    }

    private function getQuoteCaptureCacheKey(int $userId, int $chatId, int $leadId): string
    {
        return 'chatQuoteCapture' . $userId . '.' . $chatId . '.' . $leadId;
    }

    public function actionResetUnreadMessage()
    {
        $chatId = (int) Yii::$app->request->post('chatId');
        $unread = ClientChatUnread::find()->select(['*', 'cch_owner_user_id as ownerId'])->andWhere(['ccu_cc_id' => $chatId])->innerJoinWith('chat', false)->one();

        if (!$unread) {
            return $this->asJson(['error' => false, 'message' => '']);
        }

        if (!$unread->isOwner(Auth::id())) {
            return $this->asJson(['error' => true, 'message' => 'Owner incorrect']);
        }

        if (!$unread->ccu_count) {
            return $this->asJson(['error' => false, 'message' => '']);
        }

        try {
            if ($unread->delete()) {
                return $this->asJson(['error' => false, 'message' => '']);
            }
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Client chat unread message reset',
                'model' => $unread->getAttributes(),
                'errors' => $unread->getErrors(),
            ], 'ClientChatController:actionResetUnreadMessage');

            return $this->asJson(['error' => true, 'message' => 'Reset unread messages error.']);
        }
    }

    public function actionAddActiveConnection()
    {
        $chatId = (int) Yii::$app->request->post('chatId');
        $connectionId = (int) Yii::$app->request->post('connectionId');

        if (!$chat = ClientChat::find()->andWhere(['cch_id' => $chatId])->one()) {
            return $this->asJson(['error' => true, 'message' => 'Client chat not found']);
        }

        if (!UserConnection::find()->andWhere(['uc_id' => $connectionId])->exists()) {
            return $this->asJson(['error' => true, 'message' => 'User connection not found']);
        }

        if (!$chat->isOwner(Auth::id())) {
            return $this->asJson(['error' => true, 'message' => 'Owner incorrect']);
        }

        if ($activeConnection = UserConnectionActiveChat::find()->andWhere(['ucac_conn_id' => $connectionId])->one()) {
            if ($activeConnection->ucac_chat_id === $chatId) {
                return $this->asJson(['error' => false, 'message' => '']);
            }
            $activeConnection->ucac_chat_id = $chatId;
        } else {
            $activeConnection = new UserConnectionActiveChat();
            $activeConnection->ucac_chat_id = $chatId;
            $activeConnection->ucac_conn_id = $connectionId;
        }

        try {
            if ($activeConnection->save()) {
                return $this->asJson(['error' => false, 'message' => '']);
            }

            Yii::error([
                'message' => 'Add user connection active chat',
                'model' => $activeConnection->getAttributes(),
                'errors' => $activeConnection->getErrors(),
            ], 'ClientChatController:actionAddActiveConnection');

            return $this->asJson(['error' => true, 'message' => 'Active connection save error.']);
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Add user connection active chat',
                'model' => $activeConnection->getAttributes(),
                'errors' => $e->getMessage(),
            ], 'ClientChatController:actionAddActiveConnection');

            return $this->asJson(['error' => true, 'message' => 'Active connection save error.']);
        }
    }

    public function actionRemoveActiveConnection()
    {
        $chatId = (int) Yii::$app->request->post('chatId');
        $connectionId = (int) Yii::$app->request->post('connectionId');

        if (!$chat = ClientChat::find()->andWhere(['cch_id' => $chatId])->one()) {
            return $this->asJson(['error' => true, 'message' => 'Client chat not found']);
        }

        if (!$chat->isOwner(Auth::id())) {
            return $this->asJson(['error' => true, 'message' => 'Owner incorrect']);
        }

        if (!$activeConnection = UserConnectionActiveChat::find()->andWhere(['ucac_conn_id' => $connectionId, 'ucac_chat_id' => $chatId])->one()) {
            return $this->asJson(['error' => false, 'message' => '']);
        }

        try {
            if ($activeConnection->delete()) {
                return $this->asJson(['error' => false, 'message' => '']);
            }

            Yii::error([
                'message' => 'Remove user connection active chat',
                'model' => $activeConnection->getAttributes(),
                'errors' => $activeConnection->getErrors(),
            ], 'ClientChatController:actionRemoveActiveConnection');

            return $this->asJson(['error' => true, 'message' => 'Active connection remove error.']);
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Remove user connection active chat',
                'model' => $activeConnection->getAttributes(),
                'errors' => $e->getMessage(),
            ], 'ClientChatController:actionRemoveActiveConnection');

            return $this->asJson(['error' => true, 'message' => 'Active connection remove error.']);
        }
    }
}
