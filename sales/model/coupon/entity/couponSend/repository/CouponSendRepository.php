<?php

namespace sales\model\coupon\entity\couponSend\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\coupon\entity\couponSend\CouponSend;

/**
 * Class CouponSendRepository
 */
class CouponSendRepository
{
    public function save(CouponSend $model): CouponSend
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
