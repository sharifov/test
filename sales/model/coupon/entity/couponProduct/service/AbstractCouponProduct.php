<?php

namespace sales\model\coupon\entity\couponProduct\service;

use yii\base\Model;

/**
 * Class AbstractCouponProduct
 */
abstract class AbstractCouponProduct extends Model
{
    public function formName()
    {
        return '';
    }
}
