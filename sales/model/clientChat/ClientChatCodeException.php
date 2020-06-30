<?php


namespace sales\model\clientChat;

use common\CodeExceptionsModule as Module;

class ClientChatCodeException
{
	public const RC_ASSIGN_AGENT_FAILED = Module::CLIENT_CHAT . 100;
	public const CC_OWNER_ALREADY_ASSIGNED = Module::CLIENT_CHAT . 101;

	private const WARNING_ERROR_LIST = [
		self::CC_OWNER_ALREADY_ASSIGNED
	];

	public static function isRcAssignAgentFailed(\Throwable $e): bool
	{
		return $e->getCode() === self::RC_ASSIGN_AGENT_FAILED;
	}

	public static function isWarningMessage(\Throwable $e): bool
	{
		return in_array($e->getCode(), self::WARNING_ERROR_LIST, false);
	}
}