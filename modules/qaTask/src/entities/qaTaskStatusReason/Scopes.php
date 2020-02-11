<?php

namespace modules\qaTask\src\entities\qaTaskStatusReason;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

/**
 * @see QaTaskStatusReason
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['tsr_enabled' => true]);
    }

    public function byObjectType(int $objectTypeId): self
    {
        return $this->andWhere(['tsr_object_type_id' => $objectTypeId]);
    }

    public function list(): self
    {
        return $this->select(['tsr_name', 'tsr_id'])->orderBy(['tsr_id' => SORT_ASC])->indexBy('tsr_id')->asArray();
    }

    public function processing(): self
    {
        return $this->andWhere(['tsr_status_id' => QaTaskStatus::PROCESSING]);
    }

    public function listWithFullDescription(): self
    {
        return $this->select(['tsr_object_type_id', 'tsr_status_id', 'tsr_name', 'tsr_id'])->orderBy(['tsr_id' => SORT_ASC])->indexBy('tsr_id')->asArray();
    }
}
