<?php


namespace sales\guards\clientChat;


use common\models\Employee;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;

/**
 * Class ClientChatManageGuard
 * @package sales\guards\clientChat
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