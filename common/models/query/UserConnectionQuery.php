<?php

namespace common\models\query;

use common\models\UserConnection;

/**
 * This is the ActiveQuery class for [[UserConnection]].
 *
 * @see UserConnection
 */
class UserConnectionQuery extends \yii\db\ActiveQuery
{
    public function byId(int $id): self
    {
        return $this->andWhere(['uc_id' => $id]);
    }

    /**
     * {@inheritdoc}
     * @return UserConnection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserConnection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
