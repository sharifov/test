<?php
namespace frontend\controllers;

use common\models\Notifications;
use common\models\VisitorLog;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\NotFoundException;
use sales\services\clientChatMessage\ClientChatMessageService;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatController
 * @package frontend\controllers
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatMessageService $clientChatMessageService
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

	public function __construct(
		$id,
		$module,
		ClientChatRepository $clientChatRepository,
		ClientChatUserAccessRepository $clientChatUserAccessRepository,
		ClientChatMessageService $clientChatMessageService,
		$config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatMessageService = $clientChatMessageService;
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

	public function actionIndex(int $channelId = null, int $page = 1, string $rid = '')
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
				$dataProvider->pagination->pageSize = $page*self::CLIENT_CHAT_PAGE_SIZE;
				$dataProvider->pagination->page = 0;
			}
		}

		try {
			$clientChat = $this->clientChatRepository->findByRid($rid);
			$this->clientChatMessageService->discardUnreadMessages($clientChat->cch_id, $clientChat->cch_owner_user_id);
		} catch (NotFoundException $e) {
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
					'clientChatRid' => $clientChat ? $clientChat->cch_rid : ''
				]);
				$response['page'] = $page+1;
			}

			return $this->asJson($response);
		}


		return $this->render('index', [
			'channels' => $channels,
			'dataProvider' => $dataProvider,
			'channelId' => $channelId,
			'page' => $page,
			'clientChat' => $clientChat,
			'client' => $clientChat->cchClient ?? ''
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

			$cch = $this->clientChatUserAccessRepository->findByPrimaryKeys($cchId, Auth::id());
			$this->clientChatUserAccessRepository->updateStatus($cch, (int)$accessAction);

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
}