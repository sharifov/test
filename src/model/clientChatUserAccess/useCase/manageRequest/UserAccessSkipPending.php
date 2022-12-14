<?php

namespace src\model\clientChatUserAccess\useCase\manageRequest;

use src\guards\clientChat\ClientChatManageGuard;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\permissions\ClientChatActionPermission;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;

/**
 * Class UserAccessSkipPending
 * @package src\model\clientChatUserAccess\useCase\manageRequest
 *
 * @property ClientChat $chat
 * @property ClientChatUserAccess $access
 * @property int $accessStatusId
 * @property ClientChatUserAccessService $accessService
 * @property ClientChatActionPermission $actionPermission
 */
class UserAccessSkipPending implements UserAccessManageRequestInterface
{
    private ClientChat $chat;
    private ClientChatUserAccess $access;
    private int $accessStatusId;
    private ClientChatUserAccessService $accessService;
    private ClientChatActionPermission $actionPermission;

    public function __construct(ClientChat $chat, ClientChatUserAccess $access, int $accessStatusId)
    {
        $this->chat = $chat;
        $this->access = $access;
        $this->accessStatusId = $accessStatusId;
        $this->accessService = \Yii::createObject(ClientChatUserAccessService::class);
        $this->actionPermission = \Yii::createObject(ClientChatActionPermission::class);
    }

    public function handle(): void
    {
        if (!$this->actionPermission->canSkipPending($this->chat)) {
            throw new \DomainException('Action is not allowed');
        }
        $this->accessService->skipPending($this->chat, $this->access, $this->accessStatusId);
    }
}
