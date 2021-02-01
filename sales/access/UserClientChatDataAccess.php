<?php

namespace sales\access;

use sales\auth\Auth;

/**
 * Class UserClientChatDataAccess
 */
class UserClientChatDataAccess
{
    public static function isRocketChatCredentials(): bool
    {
        return (
            Auth::can('/employee/register-to-rocket-chat') &&
            Auth::can('/employee/un-register-from-rocket-chat') &&
            Auth::can('/employee/activate-to-rocket-chat') &&
            Auth::can('/employee/deactivate-from-rocket-chat')
        );
    }
}
