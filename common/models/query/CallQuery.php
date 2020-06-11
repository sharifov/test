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

	/**
	 * @param string $callSid
	 * @param int $id
	 * @return CallQuery
	 */
    public function byCallSidOrCallId(string $callSid, int $id): CallQuery
	{
		return $this->andWhere(['c_call_sid' => $callSid])->orWhere(['c_id' => $id]);
	}
}
