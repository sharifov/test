<?php

namespace modules\order\src\entities\order;

/**
 * @see Order
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byGid(string $gid): Scopes
    {
        return $this->andWhere(['or_gid' => $gid]);
    }
}
