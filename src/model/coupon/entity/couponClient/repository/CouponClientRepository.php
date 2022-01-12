<?php

namespace src\model\coupon\entity\couponClient\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\coupon\entity\couponClient\CouponClient;

/**
 * Class CouponClientRepository
 */
class CouponClientRepository
{
    public function save(CouponClient $model): CouponClient
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
