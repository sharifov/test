<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;

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
        return $this->select(['tc_name', 'tc_object_type_id', 'tc_id'])->orderBy(['tc_id' => SORT_ASC])->indexBy('tc_id')->asArray();
    }

    public function byLead(): self
    {
        return $this->byType(QaTaskObjectType::LEAD);
    }

    public function byType(int $typeId): self
    {
        return $this->andWhere(['tc_object_type_id' => $typeId]);
    }

    public function byKey(string $key): self
    {
        return $this->andWhere(['tc_key' => $key]);
    }
}
