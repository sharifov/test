<?php
namespace sales\services\clientChatService;

use common\models\Department;
use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\auth\Auth;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatCase\entity\ClientChatCaseRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\ClientChatLeadRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\TransactionManager;
use yii\helpers\ArrayHelper;
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
 * @property ClientChatLeadRepository $clientChatLeadRepository
 * @property ClientChatCaseRepository $clientChatCaseRepository
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
	/**
	 * @var ClientChatLeadRepository
	 */
	private ClientChatLeadRepository $clientChatLeadRepository;
	/**
	 * @var ClientChatCaseRepository
	 */
	private ClientChatCaseRepository $clientChatCaseRepository;

	public function __construct(
		ClientChatChannelRepository $clientChatChannelRepository,
		ClientChatRepository $clientChatRepository,
		TransactionManager $transactionManager,
		VisitorLogRepository $visitorLogRepository,
		ClientChatUserAccessRepository $clientChatUserAccessRepository,
		ClientChatVisitorRepository $clientChatVisitorRepository,
		ClientChatLeadRepository $clientChatLeadRepository,
		ClientChatCaseRepository $clientChatCaseRepository
	){
		$this->clientChatChannelRepository = $clientChatChannelRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->transactionManager = $transactionManager;
		$this->visitorLogRepository = $visitorLogRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatVisitorRepository = $clientChatVisitorRepository;
		$this->clientChatLeadRepository = $clientChatLeadRepository;
		$this->clientChatCaseRepository = $clientChatCaseRepository;
	}

	public function assignClientChatChannel(ClientChat $clientChat, int $priority): void
	{
		$clientChatChannel = $this->clientChatChannelRepository->findByClientChatData($clientChat->cch_dep_id, $clientChat->cch_project_id, $priority);
		$clientChat->cch_channel_id = $clientChatChannel->ccc_id;
	}

	/**
	 * @param ClientChat $clientChat
	 */
	public function sendRequestToUsers(ClientChat $clientChat): void
	{
		if ($channel = $clientChat->cchChannel) {
			$userChannel = ClientChatUserChannel::find()->byChannelId($channel->ccc_id)->all();

			if ($userChannel) {
				/** @var ClientChatUserChannel $item */
				foreach ($userChannel as $item) {
					$this->sendRequestToUser($clientChat, $item);
				}
			}
		}
	}

	/**
	 * @param ClientChat $clientChat
	 * @param ClientChatUserChannel $clientChatUserChannel
	 */
	public function sendRequestToUser(ClientChat $clientChat, ClientChatUserChannel $clientChatUserChannel): void
	{
		if ($clientChat->cch_owner_user_id !== $clientChatUserChannel->ccuc_user_id && $clientChatUserChannel->ccucUser->userProfile && $clientChatUserChannel->ccucUser->userProfile->isRegisteredInRc()) {
			$clientChatUserAccess = ClientChatUserAccess::create($clientChat->cch_id, $clientChatUserChannel->ccuc_user_id);
			$clientChatUserAccess->pending();
			$this->clientChatUserAccessRepository->save($clientChatUserAccess);
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

			if ($clientChat->cch_dep_id === $form->depId && !$form->agentId) {
				throw new \DomainException('Chat already assigned to this department; Choose another;');
			}

			foreach ($form->agentId as $agentId) {
				if ($clientChat->cch_owner_user_id === $agentId) {
					throw new \DomainException($clientChat->cchOwnerUser->nickname . ' is already the owner of this chat.');
				}
			}

			if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
				throw new \RuntimeException('Visitor RC id is not found');
			}

			$oldDepartment = $clientChat->cchDep->dep_name ?? null;
			$newDepartment = Department::findOne(['dep_id' => $form->depId]);

			if (!$oldDepartment || !$newDepartment) {
				throw new \RuntimeException('Old or New department name is undefined');
			}

//			$botTransferChatResult = \Yii::$app->chatBot->transferDepartment($clientChat->cch_rid, $clientChat->ccv->ccvCvd->cvd_visitor_rc_id, $oldDepartment, $newDepartment->dep_name);
//			if ($botTransferChatResult['error']) {
//				throw new \RuntimeException('[Chat Bot] ' . $botTransferChatResult['error']['message'] ?? 'Cant read error message from Chat Bot response');
//			}
//
//			$success = $botTransferChatResult['data']['success'] ?? false;
//			if (!$success) {
//				throw new \RuntimeException('[Chat Bot] ' . ($botTransferChatResult['data']['message'] ?? 'Cant read error message from Chat Bot response'));
//			}

//			$clientChat->transfer();
//			$this->clientChatRepository->save($clientChat);

//			$dto = ClientChatCloneDto::feelInOnTransfer($clientChat, $form);
//			$newClientChat = $this->clientChatRepository->clone($dto);
//			$this->clientChatRepository->save($newClientChat);
//			$this->cloneLead($clientChat, $newClientChat)->cloneCase($clientChat, $newClientChat)->assignToChannel($newClientChat);
//
//			$oldVisitor = $clientChat->ccv->ccvCvd ?? null;
//
//			if ($oldVisitor) {
//				$this->clientChatVisitorRepository->create($newClientChat->cch_id, $oldVisitor->cvd_id, $newClientChat->cch_client_id);
//			}

			$clientChat->transfer();
			$clientChat->cch_dep_id = $form->depId;
			$this->clientChatRepository->save($clientChat);

			if ($form->agentId) {
				foreach ($form->agentId as $agentId) {
					$clientChatChannel = $this->clientChatChannelRepository->findByClientChatData($form->depId, $clientChat->cch_project_id, null);
					$userChannel = ClientChatUserChannel::find()->byChannelId($clientChatChannel->ccc_id)->byUserId($agentId)->one();
					if ($userChannel) {
						try {
							$this->sendRequestToUser($clientChat, $userChannel);
						} catch (\RuntimeException $e) {
							\Yii::error('Send notification to user ' . $userChannel->ccuc_user_id . ' failed... ' . $e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'ClientChatService::transfer::RuntimeException');
							throw $e;
						}
					}
				}
			} else {
				$this->sendRequestToUsers($clientChat);
			}

			return $newDepartment;
		});
	}

	public function finishTransfer(ClientChat $clientChat, ClientChatUserAccess $chatUserAccess): ClientChat
	{
		return $this->transactionManager->wrap( function () use ($clientChat, $chatUserAccess) {
			$oldDepartment = $clientChat->cchChannel->cccDep ?? null;
			$newDepartment = Department::findOne(['dep_id' => $clientChat->cch_dep_id]);

			if (!$oldDepartment || !$newDepartment) {
				throw new \RuntimeException('Old or New department name is undefined');
			}

			$clientChat->close();
			$this->clientChatRepository->save($clientChat);

			$dto = ClientChatCloneDto::feelInOnTransfer($clientChat);
			$newClientChat = $this->clientChatRepository->clone($dto);
			$newClientChat->assignOwner($chatUserAccess->ccua_user_id);
			$this->clientChatRepository->save($newClientChat);
			$this->cloneLead($clientChat, $newClientChat)->cloneCase($clientChat, $newClientChat)->assignClientChatChannel($newClientChat, 1);
			$this->clientChatRepository->save($newClientChat);

			$userAccess = ClientChatUserAccess::create($newClientChat->cch_id, $newClientChat->cch_owner_user_id);
			$userAccess->accept();
			$this->clientChatUserAccessRepository->save($userAccess);

			$oldVisitor = $clientChat->ccv->ccvCvd ?? null;

			if ($oldVisitor) {
				$this->clientChatVisitorRepository->create($newClientChat->cch_id, $oldVisitor->cvd_id, $newClientChat->cch_client_id);
			}
			$chatUserAccess->transferAccepted();

			if ($oldDepartment->dep_id !== $newDepartment->dep_id) {
				$botTransferChatResult = \Yii::$app->chatBot->transferDepartment($clientChat->cch_rid, $clientChat->ccv->ccvCvd->cvd_visitor_rc_id, $oldDepartment->dep_name, $newDepartment->dep_name);
				if ($botTransferChatResult['error']) {
					throw new \RuntimeException('[Chat Bot] ' . $botTransferChatResult['error']['message'] ?? 'Cant read error message from Chat Bot response');
				}

				$success = $botTransferChatResult['data']['success'] ?? false;
				if (!$success) {
					throw new \RuntimeException('[Chat Bot] ' . ($botTransferChatResult['data']['message'] ?? 'Cant read error message from Chat Bot response'));
				}

			}

			$this->assignAgentToRcChannel($newClientChat->cch_rid, $newClientChat->cchOwnerUser->userProfile->up_rc_user_id ?? '');

			$data = ClientChatAccessMessage::agentTransferAccepted($clientChat, $userAccess->ccuaUser);
			Notifications::publish('clientChatTransfer', ['user_id' => $clientChat->cch_owner_user_id], ['data' => $data]);

			return $newClientChat;
		});
	}

	public function cancelTransfer(ClientChat $clientChat): void
	{
		$channel = $clientChat->cchChannel;
		if ($channel) {
			$clientChat->cch_dep_id = $channel->ccc_dep_id;
			$clientChat->generated();
			$this->clientChatRepository->save($clientChat);
		}
	}

	public function assignToChannel(ClientChat $clientChat): void
	{
		try {
			$this->assignClientChatChannel($clientChat, 1);
			$this->clientChatRepository->save($clientChat);
			$this->sendRequestToUsers($clientChat);
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

	/**
	 * @param ClientChat $oldClientChat
	 * @param ClientChat $newClientChat
	 * @return ClientChatService
	 */
	public function cloneLead(ClientChat $oldClientChat, ClientChat $newClientChat): self
	{
		$leads = $oldClientChat->leads;
		foreach ($leads as $lead) {
			$clientChatLead = ClientChatLead::create($newClientChat->cch_id, $lead->id, new \DateTimeImmutable('now'));
			$this->clientChatLeadRepository->save($clientChatLead);
		}
		return $this;
	}

	/**
	 * @param ClientChat $oldClientChat
	 * @param ClientChat $newClientChat
	 * @return ClientChatService
	 */
	public function cloneCase(ClientChat $oldClientChat, ClientChat $newClientChat): self
	{
		$cases = $oldClientChat->cases;
		foreach ($cases as $case) {
			$clientChatCase = ClientChatCase::create($newClientChat->cch_id, $case->cs_id, new \DateTimeImmutable('now'));
			$this->clientChatCaseRepository->save($clientChatCase);
		}
		return $this;
	}

}