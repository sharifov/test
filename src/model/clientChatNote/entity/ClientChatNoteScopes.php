<?php

namespace src\model\clientChatNote\entity;

/**
 * This is the ActiveQuery class for [[ClientChatNote]].
 *
 * @see ClientChatNote
 */
class ClientChatNoteScopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientChatNote[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientChatNote|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
