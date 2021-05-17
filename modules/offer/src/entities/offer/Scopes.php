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

    public function exceptStatuses(array $statuses): self
    {
        return $this->andWhere(['NOT IN', 'of_status_id', $statuses]);
    }
}
