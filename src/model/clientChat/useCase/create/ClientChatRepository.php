<?php

namespace src\model\clientChat\useCase\create;

use common\models\Employee;
use src\dispatchers\EventDispatcher;
use src\model\clientChat\ClientChatCodeException;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\repositories\department\DepartmentRepository;
use src\repositories\NotFoundException;
use src\repositories\project\ProjectRepository;
use yii\db\ActiveRecord;

/**
 * Class ClientChatRepository
 * @package src\model\clientChat\useCase\create
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

    /**
     * @param string $rid
     * @return ClientChat
     */
    public function findByRid(string $rid): ClientChat
    {
        if ($clientChat = ClientChat::find()->andWhere(['cch_rid' => $rid])->orderBy(['cch_id' => SORT_DESC])->one()) {
            return $clientChat;
        }
        throw new NotFoundException('unable to find client chat by rid: ' . $rid);
    }

    /**
     * @param string $rid
     * @return null|ClientChat
     */
    public function findLastByRid(string $rid): ?ClientChat
    {
        if ($clientChat = ClientChat::find()->byRid($rid)->orderBy(['cch_id' => SORT_DESC])->one()) {
            return $clientChat;
        }
        throw new NotFoundException('Not find client chat by rid: ' . $rid);
    }

    /**
     * @param string $rid
     * @return ClientChat
     */
    public function findNotClosed(string $rid): ClientChat
    {
        if ($clientChat = ClientChat::find()->byRid($rid)->notClosed()->notArchived()->orderBy(['cch_id' => SORT_DESC])->one()) {
            return $clientChat;
        }
        throw new NotFoundException('unable to find client chat that is not closed by rid: ' . $rid);
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

    /**
     * @param int $id
     * @return ClientChat
     */
    public function findById(int $id): ClientChat
    {
        if ($clientChat = ClientChat::findOne($id)) {
            return $clientChat;
        }
        throw new NotFoundException('Client chat is not found');
    }

    /**
     * @param string $rid
     * @return null|ClientChat
     */
    public function getLastByRid(string $rid): ?ClientChat
    {
        if ($clientChat = ClientChat::find()->byRid($rid)->orderBy(['cch_id' => SORT_DESC])->one()) {
            return $clientChat;
        }
        return null;
    }

    /**
     * @param string $rid
     * @param null|string $ownerUsername
     * @return ClientChat
     */
    public function getByRidAndOwnerUsername(string $rid, ?string $ownerUsername): ClientChat
    {
        $query = ClientChat::find()->alias('cc')->andWhere(['cch_rid' => $rid])->orderBy(['cch_id' => SORT_DESC]);
        if (!is_null($ownerUsername)) {
            $query->leftJoin(['e' => Employee::tableName()], 'cc.cch_owner_user_id=e.id');
            $query->where('e.username=:username', [':username' => $ownerUsername]);
        }
        return $query->one();
    }

    public function save(ClientChat $clientChat): ClientChat
    {
        if (!$clientChat->save()) {
            throw new \RuntimeException($clientChat->getErrorSummary(false)[0]);
        }
        $this->eventDispatcher->dispatchAll($clientChat->releaseEvents());
        return $clientChat;
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

    public function delete(ClientChat $clientChat): bool
    {
        if (!$clientChat->delete()) {
            throw new \RuntimeException($clientChat->getErrorSummary(false)[0]);
        }
        return true;
    }
}
