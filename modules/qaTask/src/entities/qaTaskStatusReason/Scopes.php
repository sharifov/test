<?php

namespace modules\qaTask\src\entities\qaTaskStatusReason;

/**
 * @see QaTaskStatusReason
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['tsr_enabled' => true]);
    }

    public function list(): self
    {
        return $this->select(['tsr_name', 'tsr_id'])->orderBy(['tsr_id' => SORT_ASC])->indexBy('tsr_id')->asArray();
    }
}
