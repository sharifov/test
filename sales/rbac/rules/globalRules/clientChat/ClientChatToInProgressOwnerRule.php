<?php

namespace sales\rbac\rules\globalRules\clientChat;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

/**
 * Class ClientChatToInProgressOwnerRule
 */
class ClientChatToInProgressOwnerRule extends Rule
{
	public $name = 'ClientChatToInProgressOwnerRule';

	public function execute($user, $item, $params): bool
	{
		if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
			return false;
		}
		$chat = $params['chat'];

		return ($chat->isHold() && $chat->isOwner((int) $user));
	}
}