<?php
namespace sales\model\clientChatLastMessage;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\repositories\Repository;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class ClientChatLastMessageRepository
 */
class ClientChatLastMessageRepository extends Repository
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
            $clientChatLastMessage->cclm_dt = $clientChatMessage->ccm_sent_dt;
            $clientChatLastMessage->cclm_cch_id = $clientChatMessage->ccm_cch_id;
            $clientChatLastMessage->cclm_type_id = self::getTypeByMessage($clientChatMessage);

            try {
                $this->save($clientChatLastMessage);
                return $clientChatLastMessage;
            } catch (\Throwable $throwable) {
                Yii::error(VarDumper::dumpAsString([
                    'Throwable' => $throwable,
                    'Errors' => $clientChatLastMessage->getErrors(),
                ]),'ClientChatLastMessageRepository:createOrUpdateByMessage:save');
            }
        }
        return null;
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