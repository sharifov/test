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
