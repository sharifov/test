<?php


namespace sales\model\clientChat\entity;

class ClientChatQuery
{
    public static function existsSameChatNotClosed(string $rid): bool
    {
        return ClientChat::find()->byRid($rid)->notClosed()->exists();
    }
}
