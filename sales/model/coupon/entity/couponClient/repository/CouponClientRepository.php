<?php

namespace sales\model\coupon\entity\couponClient\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\coupon\entity\couponClient\CouponClient;

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
