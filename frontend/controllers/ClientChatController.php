<?php

namespace frontend\controllers;

use common\components\CentrifugoService;
use common\components\i18n\Formatter;
use common\components\purifier\Purifier;
use common\models\Department;
use common\models\Lead;
use common\models\Notifications;
use common\models\Project;
use common\models\Quote;
use common\models\search\LeadSearch;
use common\models\UserConnection;
use common\models\UserProfile;
use common\models\VisitorLog;
use frontend\helpers\JsonHelper;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\clientChat\ClientChatAccessWidget;
use frontend\widgets\notification\NotificationSocketWidget;
use frontend\widgets\notification\NotificationWidget;
use sales\auth\Auth;
use sales\entities\cases\CasesSearch;
use sales\entities\chat\ChatExtendedGraphsSearch;
use sales\entities\chat\ChatFeedbackGraphSearch;
use sales\entities\chat\ChatGraphsSearch;
use sales\forms\clientChat\MultipleUpdateForm;
use sales\forms\clientChat\RealTimeStartChatForm;
use sales\helpers\app\AppHelper;
use sales\helpers\app\AppParamsHelper;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\dashboard\GroupFilter;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatSearch;
use sales\model\clientChat\permissions\ClientChatActionPermission;
use sales\model\clientChat\useCase\close\ClientChatCloseForm;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\hold\ClientChatHoldForm;
use sales\model\clientChat\useCase\sendOffer\GenerateImagesForm;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatHold\ClientChatHoldRepository;
use sales\model\clientChatHold\entity\ClientChatHold;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatNote\ClientChatNoteRepository;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use sales\model\clientChatRequest\repository\ClientChatRequestRepository;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use sales\repositories\project\ProjectRepository;
use sales\services\client\ClientManageService;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatQuery;
use sales\services\clientChatService\ClientChatService;
use sales\services\clientChatService\ClientChatStatusLogService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use sales\services\TransactionManager;
use sales\viewModel\chat\ViewModelChatExtendedGraph;
use sales\viewModel\chat\ViewModelChatFeedbackGraph;
use sales\viewModel\chat\ViewModelChatGraph;
use Yii;
use yii\base\InvalidConfigException;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use function Amp\call;

/**
 * Class ClientChatController
 *
 * @package frontend\controllers
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatService $clientChatService
 * @property ClientChatUserAccessService $clientChatUserAccessService
 * @property ClientChatNoteRepository $clientChatNoteRepository
 * @property LeadRepository $leadRepository
 * @property TransactionManager $transactionManager
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ProjectRepository $projectRepository
 * @property ClientChatRequestService $clientChatRequestService
 * @property ClientChatRequestRepository $clientChatRequestRepository
 * @property ClientChatStatusLogService $clientChatStatusLogService
 * @property ClientChatStatusLogRepository $clientChatStatusLogRepository
 * @property ClientChatHoldRepository $clientChatHoldRepository
 * @property ClientManageService $clientManageService
 */
class ClientChatController extends FController
{
    private const CLIENT_CHAT_PAGE_SIZE = 10;
    private const RESERVE_CHAT_TO_PROCESS_KEY = 'reserve_chat_to_process_key_';
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

    private ClientChatStatusLogService $clientChatStatusLogService;
    private ClientChatStatusLogRepository $clientChatStatusLogRepository;
    private ClientChatHoldRepository $clientChatHoldRepository;
    /**
     * @var ClientManageService
     */
    private ClientManageService $clientManageService;

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
        ClientChatStatusLogService $clientChatStatusLogService,
        ClientChatStatusLogRepository $clientChatStatusLogRepository,
        ClientChatHoldRepository $clientChatHoldRepository,
        ClientManageService $clientManageService,
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
        $this->clientChatStatusLogService = $clientChatStatusLogService;
        $this->clientChatStatusLogRepository = $clientChatStatusLogRepository;
        $this->clientChatHoldRepository = $clientChatHoldRepository;
        $this->clientManageService = $clientManageService;
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
            'access' => [
                'allowActions' => [
                    'ajax-close',
                    'ajax-transfer-view',
                    'delete-note',
                    'create-note',
                    'ajax-hold-view',
                    'ajax-un-hold',
                    'info',
                    'ajax-data-info',
                    'ajax-history',
                    'ajax-return',
                    'ajax-take',
                    'ajax-reopen-chat'
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionIndex()
    {
        $userId = Auth::id();

        $channels = ClientChatChannel::getListByUserId($userId);

        $filter = new FilterForm($channels);

        if (!$filter->load(Yii::$app->request->get()) || !$filter->validate()) {
            $filter->loadDefaultValues();
        }

        $filter->loadDefaultValuesByPermissions();

        $page = (int)\Yii::$app->request->get('page');
        if ($page < 1) {
            $page = 1;
        }

        if ($filter->resetAdditionalFilter) {
            $filter->resetAdditionalAttributes();
        }

        $countFreeToTake = 0;
        $dataProvider = null;
        /** @var $channels ClientChatChannel[] */
        if ($channels) {
            if (Yii::$app->request->get('act') === 'select-all') {
                $chatIds = (new ClientChatSearch())->getListOfChatsIds(Auth::user(), array_keys($channels), $filter);
                return $this->asJson($chatIds);
            }

            $dataProvider = (new ClientChatSearch())->getListOfChats(Auth::user(), array_keys($channels), $filter);

            if ($filter->group === GroupFilter::FREE_TO_TAKE) {
                $countFreeToTake = $dataProvider->getTotalCount();
            } else {
                $countFreeToTake = (new ClientChatSearch())->countFreeToTake(Auth::user(), array_keys($channels), $filter);
            }
        }

        $clientChat = null;
        $accessChatError = false;
        $resetUnreadMessagesChatId = null;
        $chid = (int)Yii::$app->request->get('chid');

        if ($chid) {
            try {
                $clientChat = $this->clientChatRepository->findById($chid);

                if (!Auth::can('client-chat/view', ['chat' => $clientChat])) {
                    $accessChatError = true;
                    throw new \DomainException('You do not have access to this chat');
                }

                if ($clientChat->cch_owner_user_id && $clientChat->isOwner(Auth::id())) {
                    $this->clientChatMessageService->discardUnreadMessages(
                        $clientChat->cch_id,
                        $clientChat->cch_owner_user_id
                    );
                    $resetUnreadMessagesChatId = $clientChat->cch_rid;
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
                    $formatter = new Formatter();
                    $formatter->timeZone = Auth::user()->timezone;
                    $response['html'] = $this->renderPartial('partial/_client-chat-item', [
                        'clientChats' => $dataProvider->getModels(),
                        'clientChatId' => $clientChat ? $clientChat->cch_id : '',
                        'formatter' => $formatter,
                        'resetUnreadMessagesChatId' => $resetUnreadMessagesChatId,
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
            'actionPermissions' => new ClientChatActionPermission(),
            'countFreeToTake' => $countFreeToTake,
            'accessChatError' => $accessChatError,
            'resetUnreadMessagesChatId' => $resetUnreadMessagesChatId
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionInfo(): Response
    {
        if (!Yii::$app->request->isAjax || !$cchId = (int)Yii::$app->request->post('cch_id')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $result = [
            'html' => '',
            'message' => '',
        ];

        try {
            $clientChat = $this->clientChatRepository->findById($cchId);

            if (!Auth::can('client-chat/view', ['chat' => $clientChat])) {
                throw new ForbiddenHttpException('You don\'t have access to this chat');
            }

            if ($clientChat->hasOwner() && $clientChat->isOwner(Auth::id())) {
                $this->clientChatMessageService->discardUnreadMessages(
                    $clientChat->cch_id,
                    (int)$clientChat->cch_owner_user_id
                );
            }

            if ($clientChat->isOwner(Auth::id())) {
                $result['readonly'] = '';
            } else {
                $result['readonly'] = '&readonly=true';
            }

            $result['gotoParam'] = '/live/' . $clientChat->cch_rid . '?layout=embedded';

            $result['html'] = $this->renderAjax('partial/_client-chat-info', [
                'clientChat' => $clientChat,
                'client' => $clientChat->cchClient,
                'actionPermissions' => new ClientChatActionPermission(),
            ]);
            $permissions = new ClientChatActionPermission();
            if ($permissions->canNoteView($clientChat) || $permissions->canNoteAdd($clientChat) || $permissions->canNoteDelete($clientChat)) {
                $result['noteHtml'] = $this->renderAjax('partial/_client-chat-note', [
                    'clientChat' => $clientChat,
                    'model' => new ClientChatNote(),
                    'actionPermissions' => $permissions,
                ]);
            } else {
                $result['noteHtml'] = '';
            }
        } catch (NotFoundException $e) {
            $result['message'] = $e->getMessage();
        }

        return $this->asJson($result);
    }

    public function actionNote(): Response
    {
        $cchId = (int)Yii::$app->request->post('cch_id', 0);

        $result = [
            'html' => '',
            'message' => '',
        ];
        try {
            $clientChat = $this->clientChatRepository->findById($cchId);

            $permissions = new ClientChatActionPermission();
            if ($permissions->canNoteView($clientChat)) {
                $result['html'] = $this->renderPartial('partial/_client-chat-note', [
                    'clientChat' => $clientChat,
                    'model' => new ClientChatNote(),
                    'actionPermissions' => $permissions,
                ]);
            } else {
                $result['html'] = '';
            }
        } catch (NotFoundException $e) {
        }

        return $this->asJson($result);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateNote(): string
    {
        if (!Yii::$app->request->isAjax || !$cchId = (int)Yii::$app->request->get('cch_id')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
        } catch (NotFoundException $throwable) {
            throw new NotFoundHttpException('Client chat is not found');
        }

        $permissions = new ClientChatActionPermission();
        if (!$permissions->canNoteAdd($clientChat)) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        $model = new ClientChatNote();
        $model->ccn_user_id = Auth::id();

        if ($model->load(Yii::$app->request->post())) {
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
            'actionPermissions' => $permissions,
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteNote(): string
    {
        if (!Yii::$app->request->isAjax ||
            !Yii::$app->request->get('cch_id') ||
            !Yii::$app->request->get('ccn_id')
        ) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $cchId = (int)Yii::$app->request->get('cch_id', 0);
        $ccnId = (int)Yii::$app->request->get('ccn_id', 0);

        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
        } catch (NotFoundException $throwable) {
            throw new NotFoundHttpException('Client chat is not found');
        }

        $permissions = new ClientChatActionPermission();
        if (!$permissions->canNoteDelete($clientChat)) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        try {
            $clientChatNote = $this->clientChatNoteRepository->findById($ccnId);
            $this->clientChatNoteRepository->toggleDeleted($clientChatNote);
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
            'actionPermissions' => $permissions
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAccessManage(): \yii\web\Response
    {
        if (!Yii::$app->request->isAjax ||
            !Yii::$app->request->post('ccuaId') ||
            !Yii::$app->request->post('accessAction')
        ) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $ccuaId = (int)Yii::$app->request->post('ccuaId');
        $accessAction = (int)Yii::$app->request->post('accessAction');

        try {
            $result = [
                'success' => false,
                'notifyMessage' => '',
                'notifyTitle' => '',
                'notifyType' => '',
            ];

            $ccua = $this->clientChatUserAccessRepository->findByPrimaryKey($ccuaId);
            $clientChat = $this->clientChatRepository->findById($ccua->ccua_cch_id);
            $this->clientChatUserAccessService->updateStatus($clientChat, $ccua, $accessAction);

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


    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionAjaxDataInfo(): string
    {
        $cchId = (int)Yii::$app->request->post('cchId');
        if (!$cchId) {
            $cchId = (int)Yii::$app->request->get('cchId');
        }

        if (!Yii::$app->request->isAjax || !$cchId) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
        } catch (NotFoundException $throwable) {
            throw new NotFoundHttpException('Client chat is not found');
        }

        if (!Auth::can('client-chat/view', ['chat' => $clientChat])) {
            throw new ForbiddenHttpException('You don\'t have access to this chat');
        }

        $visitorLog = null;
        if ($clientChat->ccv && $clientChat->ccv->ccv_cvd_id) {
            $visitorLog = VisitorLog::find()->byCvdId($clientChat->ccv->ccv_cvd_id)->orderBy(['vl_created_dt' => SORT_DESC])->one();
        }

        $requestSearch = new ClientChatRequestSearch();
        $visitorId = '';
        if ($clientChat->ccv && $clientChat->ccv->ccvCvd) {
            $visitorId = $clientChat->ccv->ccvCvd->cvd_visitor_rc_id ?? '';
        }
        $data[$requestSearch->formName()]['ccr_visitor_id'] = $visitorId;
        $data[$requestSearch->formName()]['ccr_json_data'] = 'url":"http';
        $dataProviderRequest = $requestSearch->search($data);
        $dataProviderRequest->setPagination(['pageSize' => 40]);
        $dataProviderRequest->pagination->params = array_merge(Yii::$app->request->get(), ['cchId' => $cchId]);

        if ($clientChat->cchClient) {
            $leadSearch = new LeadSearch();
            $data[$leadSearch->formName()]['client_id'] = $clientChat->cchClient->id;
            $data[$leadSearch->formName()]['project_id'] = $clientChat->cch_project_id;
            $leadDataProvider = $leadSearch->search($data);
            $leadDataProvider->pagination->params = array_merge(Yii::$app->request->get(), ['cchId' => $cchId]);

            $casesSearch = new CasesSearch();
            $data[$casesSearch->formName()]['cs_client_id'] = $clientChat->cchClient->id;
            $data[$casesSearch->formName()]['cs_project_id'] = $clientChat->cch_project_id;
            $casesDataProvider = $casesSearch->search($data, Auth::user());
            $casesDataProvider->pagination->params = array_merge(Yii::$app->request->get(), ['cchId' => $cchId]);
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

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxGetChartStats(): \yii\web\Response
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

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

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxGetExtendedStatsChart(): \yii\web\Response
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

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

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxGetFeedbackStatsChart(): \yii\web\Response
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

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

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxClose(): string
    {
        if (!Yii::$app->request->isAjax || !$cchId = (int)Yii::$app->request->post('cchId')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $form = new ClientChatCloseForm();
        $form->cchId = $cchId;

        try {
            $form->load(Yii::$app->request->post());

            $chat = $this->clientChatRepository->findById((int)$form->cchId);

            $permissions = new ClientChatActionPermission();

            if (!$permissions->canClose($chat)) {
                throw new ForbiddenHttpException('You do not have access to close this chat', 403);
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
            Yii::error(
                VarDumper::dumpAsString(AppHelper::throwableLog($e, true)),
                'ClientChatController::actionAjaxClose::Throwable'
            );
            $form->addError('general', 'Internal Server Error');
        }

        return $this->renderAjax('partial/_close_chat_view', [
            'cchId' => $cchId,
            'closeForm' => $form,
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxHistory(): string
    {
        if (!Yii::$app->request->isAjax || !$chatId = (int)Yii::$app->request->post('cchId')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            //			if ($clientChat->isClosed()) {
            //				$history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
            //			}

            if (!Auth::can('client-chat/view', ['chat' => $clientChat])) {
                throw new ForbiddenHttpException('You don\'t have access to this chat');
            }
        } catch (NotFoundException $e) {
            $clientChat = null;
        }

        return $this->renderAjax('partial/_chat_history', [
            //			'history' => $history ?? null,
            'clientChat' => $clientChat,
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException|NotFoundHttpException
     */
    public function actionAjaxTransferView(): string
    {
        if (!Yii::$app->request->isAjax || !$cchId = (int)Yii::$app->request->post('cchId')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
        } catch (NotFoundException $throwable) {
            throw new NotFoundHttpException('Client chat is not found');
        }

        $permissions = new ClientChatActionPermission();

        if (!$permissions->canTransfer($clientChat)) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        $form = new ClientChatTransferForm(
            $clientChat->cch_id,
            $clientChat->cch_channel_id,
            $clientChat->cch_project_id,
            $clientChat->cch_owner_user_id
        );

        try {
            if ($form->load(Yii::$app->request->post()) && !$form->pjaxReload && $form->validate()) {
                $newChannel = $this->clientChatService->transfer($form, Auth::user());

                return '<script>
                        $("#modal-sm").modal("hide"); 
                        refreshChatPage(' . $form->chatId . '); 
                        createNotify("Success", "Chat successfully transferred to ' . $newChannel->ccc_name . ' channel. ", "success");
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


    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxHoldView(): string
    {
        if (!Yii::$app->request->isAjax || !$cchId = (int)Yii::$app->request->post('cchId')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        try {
            $clientChat = $this->clientChatRepository->findById($cchId);
        } catch (NotFoundException $throwable) {
            throw new NotFoundHttpException('Client chat is not found');
        }

        $permissions = new ClientChatActionPermission();
        if (!$permissions->canHold($clientChat)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $form = new ClientChatHoldForm();
        $form->cchId = $clientChat->cch_id;

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $clientChat->hold(Auth::id(), ClientChatStatusLog::ACTION_HOLD, $form->comment);
                $this->clientChatRepository->save($clientChat);

                if ($clientChatStatusLog = $this->clientChatStatusLogRepository->getPrevious($clientChat->cch_id)) {
                    $startDt = ClientChatHold::getStartDT();
                    $deadlineDt = ClientChatHold::convertDeadlineDTFromMinute($form->minuteToDeadline);
                    $clientChatHold = ClientChatHold::create(
                        $clientChat->cch_id,
                        $clientChatStatusLog->csl_id,
                        $deadlineDt,
                        $startDt
                    );
                    $this->clientChatHoldRepository->save($clientChatHold);
                }

                return '<script>$("#modal-sm").modal("hide"); 
                    refreshChatPage(' . $form->cchId . '); 
                    createNotify("Success", "Сhat status changed to Hold", "success");</script>';
            } catch (\Throwable $throwable) {
                $form->addError('general', 'Internal Server Error');
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'ClientChatController::actionAjaxHoldView::Throwable'
                );
            }
        }

        return $this->renderAjax('partial/_hold_view', [
            'clientChat' => $clientChat,
            'holdForm' => $form,
            'deadlineOptions' => JsonHelper::decode(Yii::$app->params['settings']['client_chat_hold_deadline_options']),
        ]);
    }


    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAjaxUnHold(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['message' => '', 'status' => 0];
            try {
                if (!$cchId = (int)Yii::$app->request->post('cchId')) {
                    throw new BadRequestHttpException('Invalid parameters', -1);
                }
                if (!$clientChat = ClientChat::findOne($cchId)) {
                    throw new NotFoundHttpException('Client chat is not found', -2);
                }
                $permissions = new ClientChatActionPermission();
                if (!$permissions->canUnHold($clientChat)) {
                    throw new ForbiddenHttpException('Access denied.', -3);
                }

                $clientChat->inProgress(Auth::id(), ClientChatStatusLog::ACTION_REVERT_TO_PROGRESS);
                $this->clientChatRepository->save($clientChat);

                $result = ['message' => 'ClientChat status changed to InProgress', 'status' => 1];
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'ClientChatController:actionAjaxUnHold:throwable'
                );
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAjaxTake(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['message' => '', 'status' => 0, 'goToClientChatId' => ''];

            try {

                if (!$cchId = (int) Yii::$app->request->post('cchId')) {
                    throw new BadRequestHttpException('Invalid parameters');
                }

                if (!$clientChat = ClientChat::findOne($cchId)) {
                    throw new NotFoundHttpException('Chat is not found');
                }

                $permission = new ClientChatActionPermission();
                if (!$permission->canTake($clientChat)) {
                    throw new ForbiddenHttpException('Access denied.');
                }

                $this->guardCanProcessChat(Auth::id(), $cchId);

                $takeClientChat = $this->clientChatService->takeClientChat($clientChat, Auth::user());
                $data = ClientChatAccessMessage::chatTaken($clientChat, $takeClientChat->cchOwnerUser->nickname);
                Notifications::pub(['chat-' . $clientChat->cch_id], 'refreshChatPage', ['data' => $data]);

                $clientChatLink = Purifier::createChatShortLink($clientChat);
                Notifications::createAndPublish(
                    $clientChat->cch_owner_user_id,
                    'Chat was taken',
                    'Client Chat was taken by ' . $takeClientChat->cchOwnerUser->nickname . ' (' . $clientChatLink . ')',
                    Notifications::TYPE_INFO,
                    true
                );

                $result['message'] = 'Client Chat was successfully taken';
                $result['status'] = 1;
                $result['goToClientChatId'] = $takeClientChat->cch_id;
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'ClientChatController:actionTake:throwable'
                );
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    public function actionAjaxReturn(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['message' => '', 'status' => 0, 'goToClientChatId' => ''];

            try {
                if (!$cchId = (int) Yii::$app->request->post('cchId')) {
                    throw new BadRequestHttpException('Invalid parameters');
                }

                if (!$clientChat = ClientChat::findOne($cchId)) {
                    throw new NotFoundHttpException('Chat is not found');
                }

                $permission = new ClientChatActionPermission();
                if (!$permission->canReturn($clientChat)) {
                    throw new ForbiddenHttpException('Access denied.');
                }

                $this->guardCanProcessChat(Auth::id(), $cchId);

                $clientChat->inProgress(Auth::id(), ClientChatStatusLog::ACTION_REVERT_TO_PROGRESS);
                $this->clientChatRepository->save($clientChat);

                $result['message'] = 'ClientChat returned to InProgress';
                $result['status'] = 1;
                $result['goToClientChatId'] = $clientChat->cch_id;

            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'ClientChatController:actionAjaxReturn:throwable'
                );
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    private function guardCanProcessChat(int $userId, int $chatId): void
    {
        $redis = Yii::$app->redis;
        $key = self::RESERVE_CHAT_TO_PROCESS_KEY . $chatId;
        $redis->setnx($key, $userId);
        $value = $redis->get($key);
        if ((int)$value === $userId) {
            $redis->expire($key, 20);
        } else {
            throw new \RuntimeException('Chat is already being processed');
        }
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
        if ($chat->hasOwner() && $chat->isOwner($userId)) {
            $this->clientChatMessageService->discardUnreadMessages($chatId, $userId);
        }
    }

    public function actionSendOfferList(): string
    {
        $chatId = (int)\Yii::$app->request->post('chat_id');
        $leadId = (int)\Yii::$app->request->post('lead_id');
        $errorMessage = '';
        $dataProvider = null;

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            if (!Auth::can('client-chat/manage', ['chat' => $clientChat])) {
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
                    $price = (int)(str_replace('.', '', $selling)) * 100;
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
        $chatId = (int)\Yii::$app->request->post('chatId');
        $leadId = (int)\Yii::$app->request->post('leadId');

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

        $chatId = (int)Yii::$app->request->post('chatId');
        $leadId = (int)Yii::$app->request->post('leadId');
        $captureKey = (int)Yii::$app->request->post('captureKey');
        $type = (string)Yii::$app->request->post('type');

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

        return $this->render(
            'real-time',
            ['host' => $host, 'projectsWithKeys' => json_encode($projectsWithKeys, true)]
        );
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxCancelTransfer(): Response
    {
        if (!Yii::$app->request->isAjax || !$cchId = (int)Yii::$app->request->post('cchId')) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $result = [
            'error' => false,
            'message' => 'Transfer cancelled successfully',
        ];

        try {
            $chat = $this->clientChatRepository->findById($cchId);

            $this->transactionManager->wrap(function () use ($chat) {
                $this->clientChatUserAccessService->disableAccessForOtherUsersBatch($chat, $chat->cch_owner_user_id);
                $this->clientChatService->cancelTransfer(
                    $chat,
                    Auth::user(),
                    ClientChatStatusLog::ACTION_ACCEPT_TRANSFER
                );
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

        if ($form->projectName) {
            $form->projectId = $this->projectRepository->getIdByProjectKey($form->projectName);
        }
        try {

            if (Yii::$app->request->isPjax && $form->load(Yii::$app->request->post()) && $form->validate()) {
                if (!$userProfile = UserProfile::findOne(['up_user_id' => Auth::id()])) {
                    throw new NotFoundException('User Profile is not found');
                }
                if (!$userProfile->isRegisteredInRc()) {
                    throw new \DomainException('You dont have rocketchat credentials');
                }

                $channel = $this->clientChatChannelRepository->find($form->channelId);

                $department = Department::find()->select(['dep_name'])->where(['dep_id' => $channel->ccc_dep_id])->asArray()->one();
                if (!$department) {
                    throw new \RuntimeException('Cannot create room: department data is not found');
                }

                $clientChatRequest = ClientChatRequest::createByAgent($form);
                $this->clientChatRequestRepository->save($clientChatRequest);
                $client = $this->clientManageService->getOrCreateByClientChatRequest($clientChatRequest, (int)$form->projectId);

                $activeChatExist = ClientChat::find()->byChannel($channel->ccc_id)->withOwner()->byClientId($client->id)->notClosed()->exists();
                if ($activeChatExist) {
                    throw new \DomainException('This visitor is already chatting with agent in ' . $department['dep_name'] . ' department');
                }

                $this->clientChatService->createByAgent(
                    $form,
                    Auth::id(),
                    $userProfile->up_rc_user_id,
                    $userProfile->up_rc_auth_token,
                    $clientChatRequest,
                    $client,
                    $channel
                );

                return '<script>$("#modal-sm").modal("hide"); createNotify("Success", "Message was successfully sent to client", "success");</script>';
            }
        } catch (\RuntimeException | \DomainException $e) {
            $form->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error(
                $e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(),
                'ClientChatController::actionRealTimeStartChat::Throwable'
            );
            $form->addError('general', 'Internal Server Error');
        }

        $domainError = '';
        $channels = [];
        $activeChannelsIds = null;
        try {
            $userChannel = ClientChatUserChannel::find()->byUserId(Auth::id())->exists();
            if (!$userChannel) {
                throw new \DomainException('It looks like you do not have access to channels');
            }

            if ($client = $this->clientManageService->detectClientFromChatRequest($form->projectId, null, null, $form->visitorId)) {
                $activeChannelsIds = ClientChat::find()->select(['cch_channel_id'])->distinct()->withOwner()->byClientId($client->id)->notClosed()->column();
            }

            $channels = $this->clientChatChannelRepository->getByUserAndProject(
                Auth::id(),
                $form->projectId,
                Department::DEPARTMENT_EXCHANGE,
                $activeChannelsIds
            );
            $channels = ArrayHelper::map($channels, 'ccc_id', 'ccc_name');
        } catch (NotFoundException | \DomainException $e) {
            $domainError = $e->getMessage();
            if ($activeChannelsIds) {
                $domainError = 'The client already has active chats on all channels to which you have access';
            }
        }

        return $this->renderAjax('partial/_real_time_start_chat', [
            'startChatForm' => $form,
            'channels' => $channels,
            'domainError' => $domainError,
        ]);
    }

    public function actionChatRequests()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Not POST data', 1);
        }
        $page = Yii::$app->request->post('page', 0);
        $countDisplayedRequests = Yii::$app->request->post('countDisplayedRequests', 0);
        $widget = ClientChatAccessWidget::getInstance();
        $widget->userId = Auth::id();
        $widget->page = (int)$page;
        $widget->countDisplayedRequests = (int)$countDisplayedRequests;
        return $this->asJson([
            'data' => $widget->fetchItems(),
            'page' => $page + 1,
            'totalItems' => $widget->getTotalItems()
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
        $chatId = (int)Yii::$app->request->post('chatId');
        $unread = ClientChatUnread::find()->select([
            '*',
            'cch_owner_user_id as ownerId'
        ])->andWhere(['ccu_cc_id' => $chatId])->innerJoinWith('chat', false)->one();

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
        $chatId = (int)Yii::$app->request->post('chatId');
        $connectionId = (int)Yii::$app->request->post('connectionId');

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
        $chatId = (int)Yii::$app->request->post('chatId');
        $connectionId = (int)Yii::$app->request->post('connectionId');

        if (!$chat = ClientChat::find()->andWhere(['cch_id' => $chatId])->one()) {
            return $this->asJson(['error' => true, 'message' => 'Client chat not found']);
        }

        if (!$chat->isOwner(Auth::id())) {
            return $this->asJson(['error' => true, 'message' => 'Owner incorrect']);
        }

        if (!$activeConnection = UserConnectionActiveChat::find()->andWhere([
            'ucac_conn_id' => $connectionId,
            'ucac_chat_id' => $chatId
        ])->one()) {
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

    public function actionAjaxReopenChat(): Response
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        $chatId = (int)Yii::$app->request->post('chatId');

        if (!$chat = ClientChat::findOne(['cch_id' => $chatId])) {
            return $this->asJson(['error' => true, 'message' => 'Chat not found']);
        }

        $permissions = new ClientChatActionPermission();
        if (!$permissions->canReopenChat($chat)) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        $chat->inProgress(Auth::id(), ClientChatStatusLog::ACTION_REOPEN);

        try {
            $this->clientChatRepository->save($chat);
        } catch (\RuntimeException $e) {
            return $this->asJson(['error' => true, 'message' => $e->getMessage()]);
        }

        $data = ClientChatAccessMessage::chatReopen($chat->cch_id, Auth::user()->nickname);
        Notifications::pub(['chat-' . $chat->cch_id], 'refreshChatPage', ['data' => $data]);

        return $this->asJson(['error' => false, 'message' => '']);
    }

    public function actionAjaxMultipleUpdate()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        $form = new MultipleUpdateForm();

        if (Yii::$app->request->isPjax && $form->load(Yii::$app->request->post()) && $form->validate()) {
            foreach ($form->chatIds as $chatId) {
                $chat = ClientChat::findOne(['cch_id' => $chatId]);
                if ($chat) {
                    $chat->cch_status_id = $form->statusId;
                    $chat->save();
                }
            }

            return '<script>sessionStorage.selectedChats = "{}"; $("#modal-sm").modal("hide"); createNotify("Success", "Chats updated successfully", "success"); setTimeout(()=>{window.location.reload();}, 1000);</script>';
        }

        $chatIds = Yii::$app->request->post('chatIds');

        $alertMessage = '';
        if (empty($chatIds)) {
            $alertMessage = 'Select the chats you want to update';
        }

        $form->chatIds = $chatIds;

        return $this->renderAjax('partial/_ajax_multiple_update_form', [
            'formMultipleUpdate' => $form,
            'alertMessage' => $alertMessage
        ]);
    }
}
