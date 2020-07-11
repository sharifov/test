<?php
namespace frontend\controllers;

use common\models\VisitorLog;
use frontend\widgets\clientChat\ClientChatAccessWidget;
use sales\auth\Auth;
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
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\NotFoundException;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use sales\viewModel\chat\ViewModelChatGraph;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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

	public function __construct(
		$id,
		$module,
		ClientChatRepository $clientChatRepository,
		ClientChatUserAccessRepository $clientChatUserAccessRepository,
		ClientChatMessageService $clientChatMessageService,
		ClientChatService $clientChatService,
		ClientChatUserAccessService $clientChatUserAccessService,
		$config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatMessageService = $clientChatMessageService;
		$this->clientChatService = $clientChatService;
		$this->clientChatUserAccessService = $clientChatUserAccessService;
	}

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	public function actionIndex(int $channelId = null, int $page = 1, int $chid = 0)
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

			$dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => self::CLIENT_CHAT_PAGE_SIZE]]);
			if (\Yii::$app->request->isGet) {
				$dataProvider->pagination->pageSize = $page * self::CLIENT_CHAT_PAGE_SIZE;
				$dataProvider->pagination->page = 0;
			}
		}

		try {
			$clientChat = $this->clientChatRepository->findById($chid);
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
			'history' => $history ?? null
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
				'client' => $clientChat->cchClient
			]);

		} catch (NotFoundException $e) {
			$result['message'] = $e->getMessage();
		}

		return $this->asJson($result);
	}

	public function actionAccessManage(): \yii\web\Response
	{
		$cchId = \Yii::$app->request->post('cchId');
		$accessAction = \Yii::$app->request->post('accessAction');

		try {
			$result = [
				'success' => false,
				'notifyMessage' => '',
				'notifyTitle' => '',
				'notifyType' => ''
			];

			$ccua = $this->clientChatUserAccessRepository->findByPrimaryKeys($cchId, Auth::id());
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

		try {
			$clientChat = $this->clientChatRepository->findById($cchId);
			$visitorLog = VisitorLog::find()->byClient($clientChat->cch_client_id ?: 0)->orderBy(['vl_created_dt' => SORT_DESC])->one();
		} catch (NotFoundException $e) {
			$clientChat = null;
			$visitorLog = null;
		}

		return $this->renderAjax('partial/_data_info', [
			'clientChat' => $clientChat,
			'visitorLog' => $visitorLog
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
			'message' => ''
		];

		try {
			$clientChat = $this->clientChatRepository->findById($cchId);
			$clientChat->close();
			$this->clientChatRepository->save($clientChat);
		} catch (NotFoundException | \RuntimeException $e) {
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}

		return $this->asJson($result);
	}

	public function actionAjaxHistory()
	{
		$chatId = \Yii::$app->request->post('cchId');

		try {
			$clientChat = $this->clientChatRepository->findById($chatId);
			if ($clientChat->isClosed()) {
				$history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
			}
		} catch (NotFoundException $e) {
			$clientChat = null;
		}

		return $this->renderAjax('partial/_chat_history', [
			'history' => $history ?? null,
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
		}

		try {
			if ($form->load(Yii::$app->request->post()) && $form->validate()) {
				$this->clientChatService->transfer($form);
				return '<script>$("#modal-sm").modal("hide"); window.location.reload();</script>';
			}
		} catch (\DomainException $e) {
			$form->addError('depId', $e->getMessage());
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

	public function actionSendOfferList(): string
    {
        $chatId = (int)\Yii::$app->request->post('cchId');
        $errorMessage = '';
        $dataProvider = null;

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            if (!$this->sendOfferCheckAccess($clientChat, Auth::user())) {
                throw new \DomainException('Access denied.');
            }
            if (!$clientChat->cch_lead_id) {
                throw new \DomainException('Chat not assigned to Lead');
            }
            $dataProvider = $this->getSendOfferProvider($clientChat);

        } catch (\DomainException $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->renderAjax('partial/_send_offer_list', [
            'dataProvider' => $dataProvider,
            'errorMessage' => $errorMessage,
            'chatId' => $chatId
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
                $captures[] = $this->generateQuoterCapture();
            }
            if (!$this->saveQuoteCaptures($captures, Auth::id(), $form->cchId)) {
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
        $chatId = (int)\Yii::$app->request->post('cchId');

        try {
            $clientChat = $this->clientChatRepository->findById($chatId);
            if (!$captures = $this->getQuoteCaptures(Auth::id(), $clientChat->cch_id)) {
                throw new \DomainException('Not found saved quote captures. Please try again.');
            }

            $message = $this->createOfferMessage($clientChat, $captures);

//        $chatBot = Yii::$app->chatBot;

//        $rocketUserId = Auth::user()->userProfile->up_rc_user_id;
//        $rocketToken = Auth::user()->userProfile->up_rc_auth_token;
//        $headers =  [
//            'X-User-Id' => $rocketUserId,
//            'X-Auth-Token' => $rocketToken,
//        ];

//        $headers = Yii::$app->rchat->getSystemAuthDataHeader();
//        $chatBot->sendMessage($data, $headers);

            Yii::$app->rchat->sendMessage($message);
            $this->removeQuoteCaptures(Auth::id(), $chatId);

        } catch (\DomainException $e) {
            $out['error'] = true;
            $out['message'] = $e->getMessage();
        }

        return $this->asJson($out);
    }

    private function createOfferMessage(ClientChat $chat, array $captures): array
    {
        $data = [
            'message' => [
                'rid' => 'f93a9c3e-e04a-4e0f-b39e-5be30f938da4',
                'attachments' => [
                    [
                        'image_url' => 'https://ichef.bbci.co.uk/news/1024/cpsprodpb/83D7/production/_111515733_gettyimages-1208779325.jpg',
                        'title' => 'Title 2',
                        'message_link' => 'https://google.com',
                        'fields' => [
                            [
                                'short' => true,
                                'title' => '1',
                                'value' => '[Link](https://google.com/) Testing out something22222222 or other',
                            ],
                        ],
                    ],
                ],
                'customTemplate' => 'carousel',
            ]
        ];
        return $data;
    }

    //todo
    private function sendOfferCheckAccess($chat, $user): bool
    {
        return true;
    }

    //todo
    private function getSendOfferProvider(ClientChat $chat): ActiveDataProvider
    {
        return $chat->cchLead->getQuotesProvider([]);
    }

    //todo
    private function generateQuoterCapture(): array
    {
        return [
            'img' => 'https://ichef.bbci.co.uk/news/1024/cpsprodpb/83D7/production/_111515733_gettyimages-1208779325.jpg',
            'hybridUrl' => 'https://google.com'
        ];
    }

    private function saveQuoteCaptures(array $captures, int $userId, int $chatId): bool
    {
        return Yii::$app->cache->set($this->getQuoteCaptureCacheKey($userId, $chatId), $captures, 600);
    }

    private function getQuoteCaptures(int $userId, int $chatId)
    {
        return Yii::$app->cache->get($this->getQuoteCaptureCacheKey($userId, $chatId));
    }

    private function removeQuoteCaptures(int $userId, int $chatId): void
    {
        if (!Yii::$app->cache->delete($this->getQuoteCaptureCacheKey($userId, $chatId))) {
            Yii::error(VarDumper::dumpAsString([
                    'message' => 'Cant remove tmp quotes captures',
                    'userId' => $userId,
                    'chatId' => $chatId
                ]),
                'ClientChatController:removeQuoteCaptures'
            );
        }
    }

    private function getQuoteCaptureCacheKey(int $userId, int $chatId): string
    {
        return 'chatQuoteCapture' . $userId . '.' . $chatId;
    }
}
