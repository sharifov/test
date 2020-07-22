<?php

namespace sales\model\clientChat;

use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;
use yii\helpers\Url;

class Formatter
{
    public static function asClientChat(ClientChat $chat): string
    {
        return Html::a(
            'chat: ' . $chat->cch_id,
            Url::to(['/client-chat-crud/view', 'id' => $chat->cch_id]),
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}
