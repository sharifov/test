<?php

namespace sales\services\clientChatChannel;

use common\CodeExceptionsModule as Module;

class ClientChatChannelCodeException
{
	public const RC_USER_INFO = Module::CLIENT_CHAT . 101;
	public const RC_USER_NOT_FOUND = Module::CLIENT_CHAT . 102;
	public const RC_CREATE_DEPARTMENT = Module::CLIENT_CHAT . 103;
	public const RC_DEPARTMENT_EXIST = Module::CLIENT_CHAT . 103;
	public const RC_DEPARTMENT_NOT_EXIST = Module::CLIENT_CHAT . 105;
    public const RC_REMOVE_DEPARTMENT = Module::CLIENT_CHAT . 106;

	private const WARNING_ERROR_LIST = [
		self::RC_DEPARTMENT_EXIST
	];

	public static function isWarningMessage(\Throwable $e): bool
	{
		return in_array($e->getCode(), self::WARNING_ERROR_LIST, false);
	}
}