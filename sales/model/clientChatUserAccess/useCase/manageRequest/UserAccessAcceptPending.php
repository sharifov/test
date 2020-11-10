<?php


namespace sales\model\clientChatUserAccess\useCase\manageRequest;

use sales\guards\clientChat\ClientChatManageGuard;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\permissions\ClientChatActionPermission;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;

/**
 * Class UserAccessAcceptPending
 * @package sales\model\clientChatUserAccess\useCase\manageRequest
 *
 * @property ClientChat $chat
 * @property ClientChatUserAccess $access
 * @property int $accessStatusId
 * @property ClientChatUserAccessService $accessService
 * @property ClientChatActionPermission $actionPermission
 */
class UserAccessAcceptPending implements UserAccessManageRequestInterface
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
        if (!$this->actionPermission->canAcceptPending($this->chat)) {
            throw new \DomainException('Action is not allowed');
        }
        $this->accessService->acceptPending($this->chat, $this->access, $this->accessStatusId);
    }
}
