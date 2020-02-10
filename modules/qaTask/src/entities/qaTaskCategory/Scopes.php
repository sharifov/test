<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

/**
 * @see QaTaskCategory
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['tc_enabled' => true]);
    }

    public function list(): self
    {
        return $this->select(['tc_name', 'tc_id'])->orderBy(['tc_id' => SORT_ASC])->indexBy('tc_id')->asArray();
    }
}
