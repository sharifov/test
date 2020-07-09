<?php
namespace sales\services\clientChatService;

use common\components\jobs\ClientChatJob;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatData\ClientChatDataRepository;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\TransactionManager;
use yii\helpers\Json;

/**
 * Class ClientChatService
 * @package sales\services\clientChatService
 *
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ClientChatRepository $clientChatRepository
 * @property TransactionManager $transactionManager
 * @property VisitorLogRepository $visitorLogRepository
 * @property ClientChatDataRepository $clientChatDataRepository
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

	public function __construct(
		ClientChatChannelRepository $clientChatChannelRepository,
		ClientChatRepository $clientChatRepository,
		TransactionManager $transactionManager,
		VisitorLogRepository $visitorLogRepository,
		ClientChatDataRepository $clientChatDataRepository
	){
		$this->clientChatChannelRepository = $clientChatChannelRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->transactionManager = $transactionManager;
		$this->visitorLogRepository = $visitorLogRepository;
		$this->clientChatDataRepository = $clientChatDataRepository;
	}

	public function assignClientChatChannel(ClientChat $clientChat, int $priority): void
	{
		$clientChatChannel = $this->clientChatChannelRepository->findByClientChatData($clientChat, $priority);
		$clientChat->cch_channel_id = $clientChatChannel->ccc_id;
	}

	/**
	 * @param ClientChat $clientChat
	 * @param ClientChatUserAccessRepository $clientChatUserAccessRepository
	 */
	public function sendNotificationToUsers(ClientChat $clientChat, ClientChatUserAccessRepository $clientChatUserAccessRepository): void
	{
		if ($channel = $clientChat->cchChannel) {
			$userChannel = ClientChatUserChannel::find()->byChannelId($channel->ccc_id)->all();

			if ($userChannel) {
				/** @var ClientChatUserChannel $user */
				foreach ($userChannel as $user) {
					$access = $clientChatUserAccessRepository->create($clientChat->cch_id, $user->ccuc_user_id);
					$clientChatUserAccessRepository->save($access);
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
			$this->cloneAdditionalData($newClientChat, $clientChat);
		});
	}

	public function assignToChannel(ClientChat $clientChat): void
	{
		$job = new ClientChatJob();
		$job->priority = 1;
		$job->clientChat = $clientChat;
		\Yii::$app->queue_job->priority(90)->push($job);
	}

	private function cloneAdditionalData(ClientChat $newClientChat, ClientChat $oldClientChat): void
	{
		if ($log = $this->visitorLogRepository->findByCchId($oldClientChat->cch_id)) {
			$newLog = $this->visitorLogRepository->clone($log);
			$this->visitorLogRepository->save($newLog);
		}

		if ($data = $this->clientChatDataRepository->findByCchId($oldClientChat->cch_id)) {
			$newData = $this->clientChatDataRepository->clone($newClientChat, $data);
			$this->clientChatDataRepository->save($newData);
		}
	}
}