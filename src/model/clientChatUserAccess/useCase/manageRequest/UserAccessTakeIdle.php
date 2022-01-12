<?php

namespace src\model\clientChatUserAccess\useCase\manageRequest;

use common\components\purifier\Purifier;
use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use src\guards\clientChat\ClientChatManageGuard;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\permissions\ClientChatActionPermission;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;

/**
 * Class UserAccessTakeIdle
 * @package src\model\clientChatUserAccess\useCase\manageRequest
 *
 * @property ClientChat $chat
 * @property Employee $owner
 * @property ClientChatUserAccessService $accessService
 * @property ClientChatUserAccess $access
 * @property int $accessStatusId
 * @property ClientChatActionPermission $actionPermission
 */
class UserAccessTakeIdle implements UserAccessManageRequestInterface
{
    private ClientChat $chat;

    private Employee $owner;

    private ClientChatUserAccessService $accessService;

    private ClientChatUserAccess $access;

    private ClientChatActionPermission $actionPermission;

    private int $accessStatusId;

    public function __construct(ClientChat $chat, ClientChatUserAccess $access, int $accessStatusId, Employee $owner)
    {
        $this->chat = $chat;
        $this->access = $access;
        $this->accessStatusId = $accessStatusId;
        $this->owner = $owner;
        $this->accessService = \Yii::createObject(ClientChatUserAccessService::class);
        $this->actionPermission = \Yii::createObject(ClientChatActionPermission::class);
    }

    public function handle(): void
    {
        if (!$this->actionPermission->canTake($this->chat)) {
            throw new \DomainException('Action is not allowed');
        }
        $this->accessService->takeIdle($this->chat, $this->access, $this->accessStatusId, $this->owner);
    }
}
