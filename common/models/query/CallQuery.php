<?php

namespace common\models\query;

use common\models\Call;

/**
 * This is the ActiveQuery class for [[Call]].
 *
 * @see Call
 */
class CallQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int $parentId
     * @return CallQuery
     */
    public function firstChild(int $parentId): CallQuery
    {
        return $this->andWhere(['c_parent_id' => $parentId])->orderBy(['c_id' => SORT_ASC])->limit(1);
    }

    /**
     * @param int $parentId
     * @return CallQuery
     */
    public function lastChild(int $parentId): CallQuery
    {
        return $this->andWhere(['c_parent_id' => $parentId])->orderBy(['c_id' => SORT_DESC])->limit(1);
    }

    public function bySid(string $sid): self
    {
        return $this->andWhere(['c_call_sid' => $sid]);
    }

    public function byCreatedUser(int $userId): self
    {
        return $this->andWhere(['c_created_user_id' => $userId]);
    }

    public function inProgress(): self
    {
        return $this->andWhere(['c_status_id' => Call::STATUS_IN_PROGRESS]);
    }
}
