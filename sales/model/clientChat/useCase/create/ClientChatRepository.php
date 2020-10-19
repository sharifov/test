<?php


namespace sales\model\clientChat\useCase\create;


use sales\dispatchers\EventDispatcher;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\repositories\department\DepartmentRepository;
use sales\repositories\NotFoundException;
use sales\repositories\project\ProjectRepository;
use yii\db\ActiveRecord;

/**
 * Class ClientChatRepository
 * @package sales\model\clientChat\useCase\create
 *
 * @property ProjectRepository $projectRepository
 * @property DepartmentRepository $departmentRepository
 * @property EventDispatcher $eventDispatcher
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
	/**
	 * @var EventDispatcher
	 */
	private EventDispatcher $eventDispatcher;

	public function __construct(ProjectRepository $projectRepository, DepartmentRepository $departmentRepository, EventDispatcher $eventDispatcher)
	{
		$this->projectRepository = $projectRepository;
		$this->departmentRepository = $departmentRepository;
		$this->eventDispatcher = $eventDispatcher;
	}

	public function getOrCreateByRequest(ClientChatRequest $clientChatRequest, int $sourceType): ClientChat
	{
		try {
			$clientChat = $this->findNotClosed($clientChatRequest->ccr_rid);
		} catch (NotFoundException $e) {
			$clientChat = new ClientChat();
			$clientChat->cch_rid = $clientChatRequest->ccr_rid;
			$clientChat->cch_ccr_id = $clientChatRequest->ccr_id;
			$clientChat->cch_project_id = $this->projectRepository->getIdByProjectKey($clientChatRequest->getProjectKeyFromData());
			$clientChat->cch_source_type_id = $sourceType;
		}

		return $clientChat;
	}

	public function findByRid(string $rid): ClientChat
	{
		if ($clientChat = ClientChat::find()->andWhere(['cch_rid' => $rid])->orderBy(['cch_id' => SORT_DESC])->one()) {
			return $clientChat;
		}
		throw new NotFoundException('unable to find client chat by rid: ' . $rid);
	}

    public function findLastByRid(string $rid): ?ActiveRecord
	{
		if ($clientChat = ClientChat::find()->byRid($rid)->orderBy(['cch_id' => SORT_DESC])->one()) {
			return $clientChat;
		}
		throw new NotFoundException('Not find client chat by rid: ' . $rid);
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
		$this->eventDispatcher->dispatchAll($clientChat->releaseEvents());
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

	public function delete(ClientChat $clientChat): bool
	{
		if (!$clientChat->delete()) {
			throw new \RuntimeException($clientChat->getErrorSummary(false)[0]);
		}
		return true;
	}
}