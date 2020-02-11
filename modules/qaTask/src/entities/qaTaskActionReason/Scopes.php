<?php

namespace modules\qaTask\src\entities\qaTaskActionReason;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

/**
 * @see QaTaskActionReason
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['tar_enabled' => true]);
    }

    public function byObjectType(int $objectTypeId): self
    {
        return $this->andWhere(['tar_object_type_id' => $objectTypeId]);
    }

    public function list(): self
    {
        return $this->select(['tar_name', 'tar_id'])->orderBy(['tar_id' => SORT_ASC])->indexBy('tar_id')->asArray();
    }

    public function processing(): self
    {
        return $this->andWhere(['tar_action_id' => QaTaskStatus::PROCESSING]);
    }

    public function listWithFullDescription(): self
    {
        return $this->select(['tar_object_type_id', 'tar_action_id', 'tar_name', 'tar_id'])->orderBy(['tar_id' => SORT_ASC])->indexBy('tar_id')->asArray();
    }
}
