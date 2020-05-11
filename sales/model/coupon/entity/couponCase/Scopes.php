<?php

namespace sales\model\coupon\entity\couponCase;

/**
 * @see CouponCase
 */
class Scopes extends \yii\db\ActiveQuery
{
	public function getByCaseId(int $id)
	{
		return $this->andWhere(['cc_case_id' => $id]);
	}
}
