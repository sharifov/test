<?php

namespace src\model\clientUserReturn\entity;

/**
 * This is the ActiveQuery class for [[ClientUserReturn]].
 *
 * @see ClientUserReturn
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientUserReturn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientUserReturn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byClient(int $clientId)
    {
        return $this->andWhere(['cur_client_id' => $clientId]);
    }

    public function byUser(int $userId)
    {
        return $this->andWhere(['cur_user_id' => $userId]);
    }
}
