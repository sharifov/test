<?php

namespace sales\repositories\clientChatUserChannel;

use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\repositories\NotFoundException;

class ClientChatUserChannelRepository
{
    /**
     * @param int $id
     * @return ClientChatUserChannel[]
     */
    public function findByUserId(int $id): array
    {
        if ($channels = $this->getChannelsByUserId($id)) {
            return $channels;
        }
        throw new NotFoundException('Not Found data in Client Chat User Channel by user id: ' . $id);
    }

    public function getChannelsByUserId(int $id): ?array
    {
        return ClientChatUserChannel::find()->byUserId($id)->all();
    }

    public function findByPrimaryKeys(int $userId, int $channelId): ClientChatUserChannel
    {
        if ($channel = ClientChatUserChannel::find()->byUserId($userId)->byChannelId($channelId)->one()) {
            return $channel;
        }
        throw new NotFoundException('Not Found data in Client Chat User Channel by primary keys');
    }
}
