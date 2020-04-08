<?php

namespace sales\model\phoneList\entity;

use yii\db\ActiveQuery;

/**
 * @see PhoneList
 */
class Scopes extends ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['pl_enabled' => true]);
    }
}
