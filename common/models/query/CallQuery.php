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

    public function byId(int $id): self
    {
        return $this->andWhere(['c_id' => $id]);
    }

    public function bySid(string $sid): self
    {
        return $this->andWhere(['c_call_sid' => $sid]);
    }

    public function selectRecordingData(): self
    {
        return $this->select(['c_recording_sid', 'c_recording_duration']);
    }

    public function byCreatedUser(int $userId): self
    {
        return $this->andWhere(['c_created_user_id' => $userId]);
    }

    public function byParentId(int $id): self
    {
        return $this->andWhere(['c_parent_id' => $id]);
    }

    public function inProgress(): self
    {
        return $this->andWhere(['c_status_id' => Call::STATUS_IN_PROGRESS]);
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

    public function missed(): self
    {
        return $this->andWhere(['c_is_new' => true, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_status_id' => Call::STATUS_NO_ANSWER]);
    }

    public function out(): self
    {
        return $this->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT]);
    }

    public function ringing(): self
    {
        return $this->andWhere(['c_status_id' => Call::STATUS_RINGING]);
    }

    /**
     * @param null $db
     * @return array|Call|null
     */
    public function one($db = null)
    {
        return parent::one($db); // TODO: Change the autogenerated stub
    }

    /**
     * @param null $db
     * @return array|Call[]
     */
    public function all($db = null)
    {
        return parent::all($db); // TODO: Change the autogenerated stub
    }
}
