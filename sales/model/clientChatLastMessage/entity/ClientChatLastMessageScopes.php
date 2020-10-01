<?php

namespace sales\model\clientChatLastMessage\entity;

/**
 * This is the ActiveQuery class for [[ClientChatLastMessage]].
 *
 * @see ClientChatLastMessage
 */
class ClientChatLastMessageScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ClientChatLastMessage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientChatLastMessage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
