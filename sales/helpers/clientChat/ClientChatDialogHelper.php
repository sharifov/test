<?php

namespace sales\helpers\clientChat;

use common\models\Employee;

class ClientChatDialogHelper
{
    public static function getAgentToken(Employee $user): string
    {
        return $user->userClientChatData->uccd_auth_token ?? '';
    }
}
