<?php

namespace frontend\helpers;

use sales\model\clientChatMessage\entity\ClientChatMessage;

/**
 * Class ChatHelper
 */
class ChatHelper
{
    public static function formattedByChatMessage(ClientChatMessage $model): string
    {
        switch ($type = $model->getByType()) {
            case $model::BY_AGENT:
                $out = '<i class="fa fa-user-secret" aria-hidden="true" title="' . $model::BY_AGENT . '"></i> ' . $model::BY_AGENT;
                break;
            case $model::BY_CLIENT:
                $out =  '<i class="fa fa-user" aria-hidden="true" title="' . $model::BY_CLIENT . '"></i> ' . $model::BY_CLIENT;
                break;
            case $model::BY_BOT:
                $out = '<i class="fa fa-android" aria-hidden="true" title="' . $model::BY_BOT . '"></i> ' . $model::BY_BOT;
                break;
            default:
                $out = $type;
        }
        return $out;
    }
}
