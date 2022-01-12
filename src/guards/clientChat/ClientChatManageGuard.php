<?php

namespace src\guards\clientChat;

use common\models\Employee;
use src\auth\Auth;
use src\model\clientChat\entity\ClientChat;
use src\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;

/**
 * Class ClientChatManageGuard
 * @package src\guards\clientChat
 *
 * @property ClientChatStatusLogRepository $statusLogRepository
 */
class ClientChatManageGuard
{
    /**
     * @var ClientChatStatusLogRepository
     */
    private ClientChatStatusLogRepository $statusLogRepository;

    public function __construct(ClientChatStatusLogRepository $statusLogRepository)
    {
        $this->statusLogRepository = $statusLogRepository;
    }

    public function isCanCancelTransfer(ClientChat $chat, Employee $user): bool
    {
        if (!$chat->isTransfer()) {
            return false;
        }
        if (!$this->isHasAccess($chat)) {
            return false;
        }
        $lastLog = $this->statusLogRepository->getPrevious($chat->cch_id);

        return $lastLog ? ($lastLog->csl_user_id === $user->id) : false;
    }

    public function isHasAccess(ClientChat $chat): bool
    {
        return Auth::can('client-chat/manage', ['chat' => $chat]);
    }
}
