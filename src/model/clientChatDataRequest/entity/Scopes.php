<?php

namespace src\model\clientChatDataRequest\entity;

/**
 * This is the ActiveQuery class for [[ClientChatDataRequest]].
 *
 * @see ClientChatDataRequest
 */
class Scopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ClientChatDataRequest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientChatDataRequest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byChatId(int $id): self
    {
        return $this->andWhere(['ccdr_chat_id' => $id]);
    }
}
