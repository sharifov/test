<?php


namespace sales\model\clientChat\useCase\create;


use common\models\Client;
use sales\behaviors\BlameableBehaviorExceptApi;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
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
		if ($clientChat = $this->findByRid($clientChatRequest->ccr_rid)) {
			$clientChat->attachBehavior('user', BlameableBehaviorExceptApi::class);
			return $clientChat;
		}

		$clientChat = new ClientChat();
		$clientChat->cch_rid = $clientChatRequest->ccr_rid;
		$clientChat->cch_ccr_id = $clientChatRequest->ccr_id;
		$clientChat->cch_project_id = $this->projectRepository->getIdByName($clientChatRequest->getProjectNameFromData());
		$department = $this->departmentRepository->find($clientChatRequest->getDepartmentIdFromData());
		$clientChat->cch_dep_id = $department ? $department->dep_id : null;
		$clientChat->generated();
		$clientChat->attachBehavior('user', BlameableBehaviorExceptApi::class);

		return $clientChat;
	}

	public function findByRid(string $rid): ?ClientChat
	{
		return ClientChat::findOne(['cch_rid' => $rid]);
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

	public function assignOwner(ClientChatUserAccess $clientChatUserAccess): void
	{
		if ($clientChatUserAccess->ccuaCch && $clientChatUserAccess->ccuaCch->cchOwnerUser) {
			throw new \DomainException('Client Chat already assigned to: ' . $clientChatUserAccess->ccuaCch->cchOwnerUser->full_name);
		}
		$clientChatUserAccess->ccuaCch->cch_owner_user_id = $clientChatUserAccess->ccua_user_id;
		$this->save($clientChatUserAccess->ccuaCch);
	}
}