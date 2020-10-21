<?php


namespace sales\model\clientChat\entity;

class ClientChatQuery
{
    public static function lastSameChat(string $rid): ClientChat
    {
        return ClientChat::find()->byRid($rid)->last()->one();
    }
}
