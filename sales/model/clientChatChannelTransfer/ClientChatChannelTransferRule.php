<?php

namespace sales\model\clientChatChannelTransfer;

use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer;

class ClientChatChannelTransferRule
{
    public function can(int $fromChannelId, int $toChannelId): bool
    {
        return ClientChatChannelTransfer::find()
            ->select(['cctr_from_ccc_id', 'cctr_to_ccc_id'])
            ->innerJoin(ClientChatChannel::tableName() . ' as channel_from', 'channel_from.ccc_id = cctr_from_ccc_id')
            ->innerJoin(ClientChatChannel::tableName() . ' as channel_to', 'channel_to.ccc_id = cctr_to_ccc_id')
            ->andWhere('channel_from.ccc_project_id = channel_to.ccc_project_id ')
            ->andWhere(['cctr_from_ccc_id' => $fromChannelId, 'cctr_to_ccc_id' => $toChannelId])
            ->exists();
    }
}
