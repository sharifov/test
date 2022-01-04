<?php

namespace sales\model\authClient\entity;

/**
 * This is the ActiveQuery class for [[AuthClient]].
 *
 * @see AuthClient
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuthClient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuthClient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byUserId(int $userId): self
    {
        return $this->andWhere(['ac_user_id' => $userId]);
    }
}
