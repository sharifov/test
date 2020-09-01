<?php
namespace frontend\controllers;

use common\components\CentrifugoService;
use common\models\Lead;
use common\models\Quote;
use common\models\search\LeadSearch;
use common\models\VisitorLog;
use frontend\widgets\clientChat\ClientChatAccessWidget;
use frontend\widgets\notification\NotificationSocketWidget;
use frontend\widgets\notification\NotificationWidget;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSearch;
use sales\entities\chat\ChatGraphsSearch;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatSearch;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\sendOffer\GenerateImagesForm;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatNote\ClientChatNoteRepository;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use sales\services\TransactionManager;
use sales\viewModel\chat\ViewModelChatGraph;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Class ClientChatController
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
		$config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatMessageService = $clientChatMessageService;
		$this->clientChatService = $clientChatService;
		$this->clientChatUserAccessService = $clientChatUserAccessService;
		$this->clientChatNoteRepository = $clientChatNoteRepository;
        $this->leadRepository = $leadRepository;
		$this->transactionManager = $transactionManager;
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

	public function actionIndex(int $channelId = null, int $page = 1, int $chid = 0, int $tab = ClientChat::TAB_ACTIVE, int $dep = 0, int $project = 0)
	{
		$channelsQuery = ClientChatChannel::find()
			->joinWithCcuc(Auth::id());
		$dataProvider = null;
		$page = $page ?: 1;
		$channelId = $channelId ?: null;
		$channels = $channelsQuery->all();

		/** @var $channels ClientChatChannel[] */
		if ($channels) {
			$query = ClientChat::find()->orderBy(['cch_created_dt' => SORT_DESC])->byOwner(Auth::id());

			if ($channelId) {
				$query->byChannel($channelId);
			} else {
				$query->byChannelIds(ArrayHelper::getColumn($channels, 'ccc_id'));
			}

			if ($dep) {
				$query->byDepartment($dep);
			}

			if ($project) {
				$query->byProject($project);
			}

			if (ClientChat::isTabActive($tab)) {
				$query->active();
			} else {
				$query->archive();
			}

			$dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => self::CLIENT_CHAT_PAGE_SIZE]]);
			if (\Yii::$app->request->isGet) {
				$dataProvider->pagination->pageSize = $page * self::CLIENT_CHAT_PAGE_SIZE;
				$dataProvider->pagination->page = 0;
			}
		}

		try {
			$clientChat = $this->clientChatRepository->findById($chid);

			if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
				throw new ForbiddenHttpException('You do not have access to this chat', 403);
			}

			if ($clientChat->cch_owner_user_id) {
				$this->clientChatMessageService->discardUnreadMessages($clientChat->cch_id, $clientChat->cch_owner_user_id);
			}

			if ($clientChat->isClosed()) {
				$history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
			}

		} catch (NotFoundException $e) {
			$clientChat = null;
		} catch (\DomainException $e) {
			$clientChat = null;
		}

		if ($dataProvider && \Yii::$app->request->isPost) {

			if (\Yii::$app->request->post('loadingChannels')) {
				$dataProvider->pagination->page = $page;
			} else {
				$dataProvider->pagination->page = $page = 0;
			}

			$response = [
				'html' => '',
				'page' => $page
			];

			if ($dataProvider->getCount()) {
				$response['html'] = $this->renderPartial('partial/_client-chat-item', [
					'clientChats' => $dataProvider->getModels(),
					'clientChatId' => $clientChat ? $clientChat->cch_id : ''
				]);
				$response['page'] = $page + 1;
			}

			return $this->asJson($response);
		}

		return $this->render('index', [
			'channels' => $channels,
			'dataProvider' => $dataProvider,
			'channelId' => $channelId,
			'page' => $page,
			'clientChat' => $clientChat,
			'client' => $clientChat->cchClient ?? '',
			'history' => $history ?? null,
			'tab' => $tab,
			'dep' => $dep,
			'project' => $project,
			'totalUnreadMessages' => $this->clientChatMessageService->getCountOfTotalUnreadMessages(Auth::id())
		]);
	}

	public function actionInfo()
	{
		$cchId = \Yii::$app->request->post('cch_id');

		$result = [
			'html' => '',
			'message' => ''
		];
		try {
			$clientChat = $this->clientChatRepository->findById($cchId);
			$this->clientChatMessageService->discardUnreadMessages($clientChat->cch_id, $clientChat->cch_owner_user_id);

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
			'message' => ''
		];
		try {
			$clientChat = $this->clientChatRepository->findById($cchId);

			$result['html'] = $this->renderPartial('partial/_client-chat-note', [
                'clientChat' => $clientChat,
                'model' => new ClientChatNote(),
            ]);

		} catch (NotFoundException $e) {}

		return $this->asJson($result);
	}

    public function actionCreateNote(): string
    {
        $cchId = Yii::$app->request->get('cch_id');
        $model = new ClientChatNote();

        $clientChat = $this->clientChatRepository->findById($cchId);

		if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
			throw new ForbiddenHttpException('You do not have access to perform this action', 403);
		}

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            try {
                $this->clientChatNoteRepository->save($model);
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable),
                'ClientChatController:actionCreateNote:save');
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
		    Yii::error(AppHelper::throwableFormatter($throwable),
		    'ClientChatController:actionDeleteNote:delete');
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
				'notifyType' => ''
			];

			$ccua = $this->clientChatUserAccessRepository->findByPrimaryKey($ccuaId);
			$this->clientChatUserAccessService->updateStatus($ccua, (int)$accessAction);

			$result['success'] = true;
		} catch (\RuntimeException | \DomainException | NotFoundException $e) {
			\Yii::error(AppHelper::throwableFormatter($e), 'ClientChatController::actionAccessManage::RuntimeException|DomainException|NotFoundException');
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
            'message' => $statsSearch->getErrorSummary(true)
        ];

        return $this->asJson($response);
    }

    public function actionReport()
    {
        $searchModel = new ClientChatSearch();
        $dataProvider = $searchModel->report(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

	public function actionAjaxClose()
	{
		$cchId = \Yii::$app->request->post('cchId');

		$result = [
			'error' => false,
			'message' => 'Room successfully closed'
		];

		try {

			$this->clientChatService->closeConversation($cchId);

			$result['tab'] = ClientChat::TAB_ARCHIVE;

		} catch (NotFoundException | \RuntimeException | ForbiddenHttpException $e) {
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		} catch (\Throwable $e) {
			Yii::error(AppHelper::throwableFormatter($e), 'ClientChatController::actionAjaxClose::Throwable');
			$result['error'] = true;
			$result['message'] = 'Internal Server Error';
		}

		return $this->asJson($result);
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
			'clientChat' => $clientChat
		]);
	}

	public function actionAjaxTransferView(): string
	{
		$cchId = Yii::$app->request->post('cchId');

		$clientChat = ClientChat::findOne($cchId);

		$form = new ClientChatTransferForm();

		if ($clientChat) {
			$form->cchId = $clientChat->cch_id;
			$form->depId = $clientChat->cch_dep_id;
			$form->isOnline = $clientChat->cch_client_online;
		}

		if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
			throw new ForbiddenHttpException('You do not have access to perform this action', 403);
		}

		try {
			if ($form->load(Yii::$app->request->post()) && !$form->pjaxReload && $form->validate()) {
				$newDepartment = $this->clientChatService->transfer($clientChat, $form);
				return '<script>$("#modal-sm").modal("hide"); refreshChatPage('.$form->cchId.', '.ClientChat::TAB_ACTIVE.'); createNotify("Success", "Chat successfully transferred to '.$newDepartment->dep_name.' department. ", "success")</script>';
			}

			if ($form->pjaxReload) {
				$clientChat->cch_dep_id = $form->depId;
				$form->pjaxReload = 0;
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
		$cchId = Yii::$app->request->post('cchId');
		$userId = Auth::id();
		$this->clientChatMessageService->discardUnreadMessages((int)$cchId, $userId);
	}

	public function actionSendOfferList(): string
    {
        $chatId = (int)\Yii::$app->request->post('chat_id');
        $leadId = (int)\Yii::$app->request->post('lead_id');
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
                    $captures[] = $capture;
                }
            }
            if (!$captures) {
                throw new \DomainException('Not generated captures. Try again.');
            }
            if (!$this->saveQuoteCaptures($captures, Auth::id(), $form->chatId, $form->leadId)) {
                throw new \DomainException('Cant tmp save quotes. Please try again later.');
            }
        } catch (\DomainException $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->renderAjax('partial/_send_offer_generate', [
            'errorMessage' => $errorMessage,
            'form' => $form,
            'captures' => $captures,
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

            $message = $this->createOfferMessage($clientChat, $captures);

            if (($rocketUserId = Auth::user()->userProfile->up_rc_user_id) && ($rocketToken = Auth::user()->userProfile->up_rc_auth_token)) {
                $headers =  [
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

    public function actionMonitor()
    {
        if(Yii::$app->request->isPost){
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
        $host = 'https://dev-livechat.travel-dev.com/visitors';
        return $this->render('real-time', ['host' => $host]);
    }

    public function actionAjaxCancelTransfer()
	{
		$cchId = Yii::$app->request->post('cchId');

		$result = [
			'error' => false,
			'message' => 'Transfer canceled successfully'
		];

		try {

			$chat = $this->clientChatRepository->findById($cchId);

			$this->transactionManager->wrap( function () use ($chat){
				$this->clientChatUserAccessService->disableAccessForOtherUsers($chat->cch_id, $chat->cch_owner_user_id);
//				$this->clientChatService->cancelTransfer($chat);
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
                        'msg' => $capture['checkoutUrl']
                    ]
                ],
                'fields' => [
                    [
                        'short' => true,
                        'title' =>  'Offer',
                        'value' => '[' . $capture['checkoutUrl'] . '](' . $capture['checkoutUrl'] . ')'
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
                ]
            ]
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
                    'img_update' => 1
                ]
            );
            $url = $mailCapture['data'];
            return [
                'img' => $url['host'] . $url['dir'] . $url['img'],
                'checkoutUrl' => $quote->getCheckoutUrlPage()
            ];
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString([
                'error' => AppHelper::throwableFormatter($e),
                'quote' => $quote->getAttributes(),
            ]),'ClientChatController:generateQuoteCapture');
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
            Yii::error(VarDumper::dumpAsString([
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
}
