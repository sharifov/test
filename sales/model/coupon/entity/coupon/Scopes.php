<?php

namespace sales\model\coupon\entity\coupon;

/**
 * @see CouponForm
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byCouponIds(array $ids): Scopes
    {
        return $this->andWhere(['IN', 'c_id', $ids]);
    }
}
