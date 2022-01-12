<?php

namespace src\model\clientChatLastMessage;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChatLastMessage\entity\ClientChatLastMessage;
use src\model\clientChatMessage\entity\ClientChatMessage;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class ClientChatLastMessageRepository
 */
class ClientChatLastMessageRepository
{
    public function save(ClientChatLastMessage $clientChatLastMessage, bool $runValidation = false): ClientChatLastMessage
    {
        if (!$clientChatLastMessage->save($runValidation)) {
            throw new \RuntimeException('ClientChatLastMessage saving failed');
        }
        return $clientChatLastMessage;
    }

    public function createOrUpdateByMessage(ClientChatMessage $clientChatMessage): ?ClientChatLastMessage
    {
        if ($clientChat = ClientChat::findOne(['cch_id' => $clientChatMessage->ccm_cch_id])) {
            if (!$clientChatLastMessage = $clientChat->lastMessage) {
                $clientChatLastMessage = new ClientChatLastMessage();
            }
            $clientChatLastMessage->cclm_message = $clientChatMessage->getMessage();
            $clientChatLastMessage->cclm_cch_id = $clientChatMessage->ccm_cch_id;
            $clientChatLastMessage->cclm_type_id = self::getTypeByMessage($clientChatMessage);
            $clientChatLastMessage->cclm_platform_id = $clientChatMessage->ccm_platform_id;

            try {
                $this->save($clientChatLastMessage);
                return $clientChatLastMessage;
            } catch (\Throwable $throwable) {
                Yii::error(VarDumper::dumpAsString([
                    'Throwable' => $throwable,
                    'Errors' => $clientChatLastMessage->getErrors(),
                ]), 'ClientChatLastMessageRepository:createOrUpdateByMessage:save');
            }
        }
        return null;
    }

    public function getByChatId(int $chatId): ?ClientChatLastMessage
    {
        return ClientChatLastMessage::findOne(['cclm_cch_id' => $chatId]);
    }

    public function cloneToNewChat(ClientChatLastMessage $clientChatLastMessage, int $chatId): ClientChatLastMessage
    {
        return ClientChatLastMessage::create(
            $chatId,
            $clientChatLastMessage->cclm_type_id,
            $clientChatLastMessage->cclm_message,
            $clientChatLastMessage->cclm_dt,
            $clientChatLastMessage->cclm_platform_id
        );
    }

    private static function getTypeByMessage(ClientChatMessage $clientChatMessage): int
    {
        if ($clientChatMessage->isMessageFromClient()) {
            return ClientChatLastMessage::TYPE_CLIENT;
        }
        if ($clientChatMessage->isMessageFromBot()) {
            return ClientChatLastMessage::TYPE_BOT;
        }
        return ClientChatLastMessage::TYPE_AGENT;
    }

    public function removeByClientChat(int $clientChatId): int
    {
        return ClientChatLastMessage::deleteAll(['cclm_cch_id' => $clientChatId]);
    }
}
