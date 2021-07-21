<?php

namespace sales\model\coupon\entity\coupon\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\coupon\entity\coupon\Coupon;

/**
 * Class CouponRepository
 */
class CouponRepository
{
    public function save(Coupon $model): Coupon
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
