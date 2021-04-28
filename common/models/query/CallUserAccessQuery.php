<?php

namespace common\models\query;

use common\models\CallUserAccess;

/**
 * This is the ActiveQuery class for [[CallUserAccess]].
 *
 * @see CallUserAccess
 */
class CallUserAccessQuery extends \yii\db\ActiveQuery
{
    public function byCall(int $callId): self
    {
        return $this->andWhere(['cua_call_id' => $callId]);
    }

    public function byWarmTransfer(): self
    {
        return $this->andWhere(['cua_status_id' => CallUserAccess::STATUS_TYPE_WARM_TRANSFER]);
    }

    public function pending(): self
    {
        return $this->andWhere(['cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING]);
    }

    public function accepted(): self
    {
        return $this->andWhere(['cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT]);
    }

    public function byUser(int $userId): self
    {
        return $this->andWhere(['cua_user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     * @return CallUserAccess[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CallUserAccess|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
