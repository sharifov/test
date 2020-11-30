<?php

namespace frontend\widgets\clientChat;

use sales\model\clientChat\entity\ClientChat;
use yii\base\Widget;

class ClientChatClientInfoWidget extends Widget
{
    public ClientChat $chat;

    public function run(): string
    {
        return $this->render('cc_client_info', ['clientChat' => $this->chat]);
    }
}
