<?php

namespace sales\services\clientChatEndConversation;

use sales\model\clientChat\entity\ClientChat;
use sales\repositories\NotFoundException;

/**
 * Class ClientChatEndConversationService
 */
class ClientChatEndConversationService
{
    public static function endConversation(int $chatId, bool $shallowClose = true): ?ClientChat
    {
        if (!$clientChat = ClientChat::findOne($chatId)) {
            throw new NotFoundException('ClientChat not found. clientChatId (' . $chatId . ')');
        }
        if (!isset($clientChat->ccv->ccvCvd->cvd_visitor_rc_id)) {
            throw new NotFoundException('Visitor RC id is not found. clientChatId (' . $chatId . ')');
        }

        $shallowCloseParam = $shallowClose;
        if (!$shallowClose && ClientChat::find()->byRid($clientChat->cch_rid)->notById($chatId)->notArchived()->exists()) {
            $shallowCloseParam = true;
        }

        $botCloseChatResult = \Yii::$app->chatBot->endConversation(
            $clientChat->cch_rid,
            $clientChat->ccv->ccvCvd->cvd_visitor_rc_id,
            $shallowCloseParam
        );
        if ($botCloseChatResult['error']) {
            $errorMessage = '[Chat Bot] ' . $botCloseChatResult['error']['message'] ?? 'Unknown error message';
            $errorMessage .= ' clientChatId (' . $chatId . ')';
            \Yii::error(
                $errorMessage,
                'ClientChatEndConversationJob:ChatBot:Error'
            );
            throw new \RuntimeException($errorMessage);
        }
        return $clientChat;
    }
}