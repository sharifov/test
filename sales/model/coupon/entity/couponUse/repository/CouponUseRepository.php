<?php

namespace sales\model\coupon\entity\couponUse\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\coupon\entity\couponUse\CouponUse;

/**
 * Class CouponUseRepository
 */
class CouponUseRepository
{
    public function save(CouponUse $model): CouponUse
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
