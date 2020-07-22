<?php
namespace sales\services\clientChatService;

use common\components\ChatBot;
use http\Exception\RuntimeException;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatData\ClientChatDataRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\TransactionManager;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * Class ClientChatService
 * @package sales\services\clientChatService
 *
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ClientChatRepository $clientChatRepository
 * @property TransactionManager $transactionManager
 * @property VisitorLogRepository $visitorLogRepository
 * @property ClientChatDataRepository $clientChatDataRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 */
class ClientChatService
{
	/**
	 * @var ClientChatChannelRepository
	 */
	private ClientChatChannelRepository $clientChatChannelRepository;
	/**
	 * @var ClientChatRepository
	 */
	private ClientChatRepository $clientChatRepository;
	/**
	 * @var TransactionManager
	 */
	private TransactionManager $transactionManager;
	/**
	 * @var VisitorLogRepository
	 */
	private VisitorLogRepository $visitorLogRepository;
	/**
	 * @var ClientChatDataRepository
	 */
	private ClientChatDataRepository $clientChatDataRepository;
	/**
	 * @var ClientChatUserAccessRepository
	 */
	private ClientChatUserAccessRepository $clientChatUserAccessRepository;

	public function __construct(
		ClientChatChannelRepository $clientChatChannelRepository,
		ClientChatRepository $clientChatRepository,
		TransactionManager $transactionManager,
		VisitorLogRepository $visitorLogRepository,
		ClientChatDataRepository $clientChatDataRepository,
		ClientChatUserAccessRepository $clientChatUserAccessRepository
	){
		$this->clientChatChannelRepository = $clientChatChannelRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->transactionManager = $transactionManager;
		$this->visitorLogRepository = $visitorLogRepository;
		$this->clientChatDataRepository = $clientChatDataRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
	}

	public function assignClientChatChannel(ClientChat $clientChat, int $priority): void
	{
		$clientChatChannel = $this->clientChatChannelRepository->findByClientChatData($clientChat, $priority);
		$clientChat->cch_channel_id = $clientChatChannel->ccc_id;
	}

	/**
	 * @param ClientChat $clientChat
	 */
	public function sendNotificationToUsers(ClientChat $clientChat): void
	{
		if ($channel = $clientChat->cchChannel) {
			$userChannel = ClientChatUserChannel::find()->byChannelId($channel->ccc_id)->all();

			if ($userChannel) {
				/** @var ClientChatUserChannel $item */
				foreach ($userChannel as $item) {
					if ($item->ccucUser->userProfile && $item->ccucUser->userProfile->isRegisteredInRc()) {
						$clientChatUserAccess = ClientChatUserAccess::create($clientChat->cch_id, $item->ccuc_user_id);
						$clientChatUserAccess->pending();
						$this->clientChatUserAccessRepository->save($clientChatUserAccess);
					}
				}
			}
		}
	}

	/**
	 * @param string $rid
	 * @param string $userId
	 * @throws \yii\httpclient\Exception
	 */
	public function assignAgentToRcChannel(string $rid, string $userId): void
	{
		$response = \Yii::$app->chatBot->assignAgent($rid, $userId);
		if ($response['error']) {
			$error = Json::decode($response['error']);
			throw new \RuntimeException($error['data']['error'], ClientChatCodeException::RC_ASSIGN_AGENT_FAILED);
		}
	}

	/**
	 * @param ClientChatTransferForm $form
	 * @throws \Throwable
	 */
	public function transfer(ClientChatTransferForm $form): void
	{
		$this->transactionManager->wrap( function () use ($form) {
			$clientChat = $this->clientChatRepository->findById($form->cchId);

			if ($clientChat->isClosed()) {
				throw new \DomainException('It’s not possible to transfer the chat to another department because it is in the "Closed" status');
			}

			if ($clientChat->cch_dep_id === $form->depId) {
				throw new \DomainException('Chat already assigned to this department; Choose another;');
			}
			$clientChat->close();
			$this->clientChatRepository->save($clientChat);

			$dto = ClientChatCloneDto::feelInOnTransfer($clientChat, $form);
			$newClientChat = $this->clientChatRepository->clone($dto);
			$this->clientChatRepository->save($newClientChat);
			$this->assignToChannel($newClientChat);
//			$this->cloneAdditionalData($newClientChat, $clientChat);
		});
	}

	public function assignToChannel(ClientChat $clientChat): void
	{
		try {
			$this->assignClientChatChannel($clientChat, 1);
			$this->clientChatRepository->save($clientChat);
			$this->sendNotificationToUsers($clientChat);
		} catch (\RuntimeException | NotFoundException $e) {
			\Yii::info('Send notification to users failed... ' . $e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'ClientChatService::assignToChannel::RuntimeException|NotFoundException');
		}
		\Yii::info('Send notification to users successfully finished...', 'ClientChatService::assignToChannel');
	}

	public function closeConversation(int $chhId): void
	{
		$clientChat = $this->clientChatRepository->findById($chhId);

		if (!$clientChat->ccv || !$clientChat->ccv->ccv_visitor_rc_id) {
			throw new \RuntimeException('Visitor RC id is not found');
		}

		$botCloseChatResult = \Yii::$app->chatBot->endConversation($clientChat->cch_rid, $clientChat->ccv->ccv_visitor_rc_id);
		\Yii::info(VarDumper::dumpAsString($botCloseChatResult, 70), 'info\closeConversation');
		if ($botCloseChatResult['error']) {
			throw new \RuntimeException('[Chat Bot] ' . $botCloseChatResult['error']);
		}

		$success = $botCloseChatResult['data']['data']['data']['success'] ?? false;
		if (!$success) {
			throw new \RuntimeException('[Chat Bot] ' . ($botCloseChatResult['data']['data']['data']['message'] ?? 'Unknown error message'));
		}

		$clientChat->close();

		$this->clientChatRepository->save($clientChat);
	}

	private function cloneAdditionalData(ClientChat $newClientChat, ClientChat $oldClientChat): void
	{
//		if ($log = $this->visitorLogRepository->findByCchId($oldClientChat->cch_id)) {
//			$newLog = $this->visitorLogRepository->clone($log);
//			$this->visitorLogRepository->save($newLog);
//		}
//
//		if ($data = $this->clientChatDataRepository->findByCchId($oldClientChat->cch_id)) {
//			$newData = $this->clientChatDataRepository->clone($newClientChat, $data);
//			$this->clientChatDataRepository->save($newData);
//		}
	}
}