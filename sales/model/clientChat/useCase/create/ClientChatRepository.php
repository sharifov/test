<?php


namespace sales\model\clientChat\useCase\create;


use sales\behaviors\BlameableBehaviorExceptApi;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\repositories\department\DepartmentRepository;
use sales\repositories\NotFoundException;
use sales\repositories\project\ProjectRepository;

/**
 * Class ClientChatRepository
 * @package sales\model\clientChat\useCase\create
 *
 * @property ProjectRepository $projectRepository
 * @property DepartmentRepository $departmentRepository
 */
class ClientChatRepository
{
	/**
	 * @var ProjectRepository
	 */
	private ProjectRepository $projectRepository;
	/**
	 * @var DepartmentRepository
	 */
	private DepartmentRepository $departmentRepository;

	public function __construct(ProjectRepository $projectRepository, DepartmentRepository $departmentRepository)
	{
		$this->projectRepository = $projectRepository;
		$this->departmentRepository = $departmentRepository;
	}

	public function getOrCreateByRequest(ClientChatRequest $clientChatRequest): ClientChat
	{
		try {
			$clientChat = $this->findNotClosed($clientChatRequest->ccr_rid);
		} catch (NotFoundException $e) {
			$clientChat = new ClientChat();
			$clientChat->cch_rid = $clientChatRequest->ccr_rid;
			$clientChat->cch_ccr_id = $clientChatRequest->ccr_id;
			$clientChat->cch_project_id = $this->projectRepository->getIdByName($clientChatRequest->getProjectNameFromData());
			$department = $this->departmentRepository->findByName($clientChatRequest->getDepartmentFromData());
			$clientChat->cch_dep_id = $department ? $department->dep_id : null;
			$clientChat->generated();
		}

		return $clientChat;
	}

	public function clone(ClientChatCloneDto $dto): ClientChat
	{
		$chat = new ClientChat();
		$chat->cch_rid = $dto->cchRid;
		$chat->cch_ccr_id = $dto->cchCcrId;
		$chat->cch_project_id = $dto->cchProjectId;
		$chat->cch_dep_id = $dto->cchDepId;
		$chat->cch_client_id = $dto->cchClientId;
		$chat->cch_owner_user_id = $dto->ownerId;
		$chat->cch_client_online = $dto->isOnline;
		$chat->generated();
		return $chat;
	}

	public function findByRid(string $rid): ClientChat
	{
		if ($clientChat = ClientChat::findOne(['cch_rid' => $rid])) {
			return $clientChat;
		}
		throw new NotFoundException('unable to find client chat by rid: ' . $rid);
	}

	public function findNotClosed(string $rid): ClientChat
	{
		if ($clientChat = ClientChat::find()->byRid($rid)->notClosed()->orderBy(['cch_id' => SORT_DESC])->one()) {
			return $clientChat;
		}
		throw new NotFoundException('unable to find client chat that is not closed by rid: ' . $rid);
	}

	public function save(ClientChat $clientChat): ClientChat
	{
		if (!$clientChat->save()) {
			throw new \RuntimeException($clientChat->getErrorSummary(false)[0]);
		}
		return $clientChat;
	}

	public function findById(int $id): ClientChat
	{
		if ($clientChat = ClientChat::findOne($id)) {
			return $clientChat;
		}
		throw new NotFoundException('Client chat is not found');
	}

	public function assignOwner(ClientChat $clientChat, int $userId): void
	{
		if ($clientChat->cchOwnerUser && $clientChat->cch_owner_user_id !== $userId) {
			throw new \DomainException('Client Chat already assigned to: ' . $clientChat->cchOwnerUser->username, ClientChatCodeException::CC_OWNER_ALREADY_ASSIGNED);
		}
		$clientChat->cch_owner_user_id = $userId;
	}

	public function isChatInTransfer(int $cchId): bool
	{
		$chat = ClientChat::find()->select(['cch_status_id'])->byId($cchId)->one();
		return ($chat ? $chat->isTransfer() : false);
	}

	/**
	 * @param int $id
	 * @return ClientChat[]
	 */
	public function findByClientId(int $id): array
	{
		if ($chats = ClientChat::find()->byClientId($id)->all()) {
			return $chats;
		}
		throw new NotFoundException('Client Chats are not found by client id: ' . $id);
	}
}