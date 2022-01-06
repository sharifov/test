<?php

namespace sales\model\userAuthClient\entity;

/**
 * This is the ActiveQuery class for [[UserAuthClient]].
 *
 * @see UserAuthClient
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserAuthClient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserAuthClient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byUserId(int $userId): self
    {
        return $this->andWhere(['uac_user_id' => $userId]);
    }
}
