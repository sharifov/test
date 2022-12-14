<?php

namespace src\model\clientChatHold\entity;

/**
 * This is the ActiveQuery class for [[ClientChatHold]].
 *
 * @see ClientChatHoldService
 */
class ClientChatHoldScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ClientChatHold[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientChatHold|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
