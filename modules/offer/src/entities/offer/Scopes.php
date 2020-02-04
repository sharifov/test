<?php

namespace modules\offer\src\entities\offer;

/**
 * @see Offer
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byGid(string $gid): self
    {
        return $this->andWhere(['of_gid' => $gid]);
    }
}
