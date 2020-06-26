<?php
namespace frontend\controllers;

use http\Exception\RuntimeException;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\NotFoundException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatController
 * @package frontend\controllers
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
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

	public function __construct($id, $module, ClientChatRepository $clientChatRepository, ClientChatUserAccessRepository $clientChatUserAccessRepository, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
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
			$query = ClientChat::find()->orderBy(['cch_created_dt' => SORT_DESC]);

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

		$clientChat = $this->clientChatRepository->findByRid($rid);

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
				'message' => ''
			];

			$cch = $this->clientChatUserAccessRepository->find($cchId);
			$this->clientChatUserAccessRepository->updateStatus($cch, (int)$accessAction);

			$result['success'] = true;
		} catch (\RuntimeException | \DomainException $e) {
			$result['message'] = $e->getMessage();
		}

		return $this->asJson($result);
	}
}