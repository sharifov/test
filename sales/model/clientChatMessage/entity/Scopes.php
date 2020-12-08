<?php

namespace sales\model\clientChatMessage\entity;

/**
 * @see ClientChatMessage
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byChatRoomId(string $rid): self
    {
        return $this->andWhere(['ccm_rid' => $rid]);
    }

    public function byChhId(int $id): self
    {
        return $this->andWhere(['ccm_cch_id' => $id]);
    }

    public function byChatIds(array $ids): self
    {
        return $this->andWhere(['ccm_cch_id' => $ids]);
    }

    public function first(int $chatId): self
    {
        return $this->andWhere(['ccm_cch_id' => $chatId])->orderBy(['ccm_id' => SORT_ASC]);
    }
}
