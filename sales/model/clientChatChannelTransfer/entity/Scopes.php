<?php

namespace sales\model\clientChatChannelTransfer\entity;

/**
 * @see ClientChatChannelTransfer
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function fromChannel(int $channelId): self
    {
        return $this->andWhere(['cctr_from_ccc_id' => $channelId]);
    }

    public function toChannel(int $channelId): self
    {
        return $this->andWhere(['cctr_to_ccc_id' => $channelId]);
    }
}
