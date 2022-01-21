<?php

namespace src\model\coupon\entity\coupon\service;

use src\model\coupon\entity\coupon\Coupon;
use src\model\coupon\entity\coupon\CouponStatus;
use yii\db\Expression;

/**
 * Class CouponService
 */
class CouponService
{
    public static function checkIsValid(string $code): bool
    {
        return Coupon::find()
            ->where(['c_code' => $code])
            ->andWhere(['IN', 'c_status_id', CouponStatus::VALID_STATUS_LIST])
            ->andWhere([
                'OR',
                    ['c_disabled' => false],
                    ['c_disabled' => null],
            ])
            ->andWhere([
                'OR',
                    ['<=', 'DATE(c_start_date)', date('Y-m-d')],
                    ['c_start_date' => null],
            ])
            ->andWhere([
                'OR',
                    ['>=', 'DATE(c_exp_date)', date('Y-m-d')],
                    ['c_exp_date' => null],
            ])
            ->andWhere([
                'OR',
                    ['c_reusable' => null],
                    ['c_reusable' => false],
                    new Expression('(c_reusable = TRUE AND c_reusable_count > c_used_count)'),
            ])
            ->exists();
    }

    public static function checkChangeStatusToUse(Coupon $coupon): bool
    {
        if ($coupon->isUsed()) {
            return false;
        }
        if (!$coupon->c_reusable) {
            return true;
        }
        if ($coupon->c_reusable_count <= $coupon->c_used_count) {
            return true;
        }
        return false;
    }

    public static function checkChangeStatusToProgress(Coupon $coupon): bool
    {
        if ($coupon->isInProgress()) {
            return false;
        }
        if ($coupon->c_reusable && ($coupon->c_reusable_count > $coupon->c_used_count)) {
            return true;
        }
        return false;
    }
}
