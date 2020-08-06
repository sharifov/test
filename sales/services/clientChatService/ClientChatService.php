<?php
namespace sales\services\clientChatService;

use common\models\Department;
use sales\auth\Auth;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\TransactionManager;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;

/**
 * Class ClientChatService
 * @package sales\services\clientChatService
 *
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ClientChatRepository $clientChatRepository
 * @property TransactionManager $transactionManager
 * @property VisitorLogRepository $visitorLogRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
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
	 * @var ClientChatUserAccessRepository
	 */
	private ClientChatUserAccessRepository $clientChatUserAccessRepository;
	/**
	 * @var ClientChatVisitorRepository
	 */
	private ClientChatVisitorRepository $clientChatVisitorRepository;

	public function __construct(
		ClientChatChannelRepository $clientChatChannelRepository,
		ClientChatRepository $clientChatRepository,
		TransactionManager $transactionManager,
		VisitorLogRepository $visitorLogRepository,
		ClientChatUserAccessRepository $clientChatUserAccessRepository,
		ClientChatVisitorRepository $clientChatVisitorRepository
	){
		$this->clientChatChannelRepository = $clientChatChannelRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->transactionManager = $transactionManager;
		$this->visitorLogRepository = $visitorLogRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatVisitorRepository = $clientChatVisitorRepository;
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
			throw new \RuntimeException('[Chat Bot] ' . $response['error']['message'] ?? 'Unknown error...', ClientChatCodeException::RC_ASSIGN_AGENT_FAILED);
		}
	}

	/**
	 * @param ClientChatTransferForm $form
	 * @return Department
	 * @throws \Throwable
	 */
	public function transfer(ClientChatTransferForm $form): Department
	{
		return $this->transactionManager->wrap( function () use ($form) {
			$clientChat = $this->clientChatRepository->findById($form->cchId);

			if ($clientChat->isClosed()) {
				throw new \DomainException('It’s not possible to transfer the chat to another department because it is in the "Closed" status');
			}

			if ($clientChat->cch_dep_id === $form->depId) {
				throw new \DomainException('Chat already assigned to this department; Choose another;');
			}

			if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
				throw new \RuntimeException('Visitor RC id is not found');
			}

			$oldDepartment = $clientChat->cchDep->dep_name ?? null;
			$newDepartment = Department::findOne(['dep_id' => $form->depId]);

			if (!$oldDepartment || !$newDepartment) {
				throw new \RuntimeException('Old or New department name is undefined');
			}

			$botTransferChatResult = \Yii::$app->chatBot->transferDepartment($clientChat->cch_rid, $clientChat->ccv->ccvCvd->cvd_visitor_rc_id, $oldDepartment, $newDepartment->dep_name);
			if ($botTransferChatResult['error']) {
				throw new \RuntimeException('[Chat Bot] ' . $botTransferChatResult['error']['message'] ?? 'Cant read error message from Chat Bot response');
			}

			$success = $botTransferChatResult['data']['success'] ?? false;
			if (!$success) {
				throw new \RuntimeException('[Chat Bot] ' . ($botTransferChatResult['data']['message'] ?? 'Cant read error message from Chat Bot response'));
			}

			$clientChat->close();
			$this->clientChatRepository->save($clientChat);

			$dto = ClientChatCloneDto::feelInOnTransfer($clientChat, $form);
			$newClientChat = $this->clientChatRepository->clone($dto);
			$this->clientChatRepository->save($newClientChat);
			$this->assignToChannel($newClientChat);

			$oldVisitor = $clientChat->ccv->ccvCvd ?? null;

			if ($oldVisitor) {
				$this->clientChatVisitorRepository->create($newClientChat->cch_id, $oldVisitor->cvd_id, $newClientChat->cch_client_id);
			}

			return $newDepartment;
		});
	}

	public function assignToChannel(ClientChat $clientChat): void
	{
		try {
			$this->assignClientChatChannel($clientChat, 1);
			$this->clientChatRepository->save($clientChat);
			$this->sendNotificationToUsers($clientChat);
		} catch (\RuntimeException | NotFoundException $e) {
			\Yii::error('Send notification to users failed... ' . $e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'ClientChatService::assignToChannel::RuntimeException|NotFoundException');
		}
	}

	public function closeConversation(int $chhId): void
	{
		$clientChat = $this->clientChatRepository->findById($chhId);

		if (!Auth::can('client-chat/manage/all', ['chat' => $clientChat])) {
			throw new ForbiddenHttpException('You do not have access to perform this action', 403);
		}

		if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
			throw new \RuntimeException('Visitor RC id is not found');
		}

		$botCloseChatResult = \Yii::$app->chatBot->endConversation($clientChat->cch_rid, $clientChat->ccv->ccvCvd->cvd_visitor_rc_id);
		if ($botCloseChatResult['error']) {
			throw new \RuntimeException('[Chat Bot] ' . $botCloseChatResult['error']['message'] ?? 'Unknown error message');
		}

		$success = $botCloseChatResult['data']['success'] ?? false;
		if (!$success) {
			throw new \RuntimeException('[Chat Bot] ' . ($botCloseChatResult['data']['message'] ?? 'Unknown error message'));
		}

		$clientChat->close();

		$this->clientChatRepository->save($clientChat);
	}
}