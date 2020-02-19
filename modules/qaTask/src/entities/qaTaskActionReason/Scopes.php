<?php

namespace modules\qaTask\src\entities\qaTaskActionReason;

/**
 * @see QaTaskActionReason
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['tar_enabled' => true]);
    }

    public function objectType(int $objectTypeId): self
    {
        return $this->andWhere(['tar_object_type_id' => $objectTypeId]);
    }

    public function action(int $actionId): self
    {
        return $this->andWhere(['tar_action_id' => $actionId]);
    }

    public function list(): self
    {
        return $this->select(['tar_name', 'tar_id', 'tar_object_type_id', 'tar_action_id', 'tar_comment_required'])->orderBy(['tar_id' => SORT_ASC])->indexBy('tar_id')->asArray();
    }
}
