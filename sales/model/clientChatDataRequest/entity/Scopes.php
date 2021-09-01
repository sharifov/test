<?php

namespace sales\model\clientChatDataRequest\entity;

/**
 * This is the ActiveQuery class for [[ClientChatDataRequest]].
 *
 * @see ClientChatDataRequest
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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
}
