<?php

namespace sales\model\coupon\useCase\apiEdit;

use sales\model\coupon\entity\coupon\Coupon;

/**
 * Class CouponApiEditService
 */
class CouponApiEditService
{
    public static function editFromApiForm(Coupon $coupon, CouponEditForm $couponEditForm): Coupon
    {
        if (!empty($couponEditForm->c_start_date)) {
            $coupon->c_start_date = $couponEditForm->c_start_date;
        }
        if (!empty($couponEditForm->c_exp_date)) {
            $coupon->c_exp_date = $couponEditForm->c_exp_date;
        }
        if (isset($couponEditForm->c_public)) {
            $coupon->c_public = $couponEditForm->c_public;
        }
        if (isset($couponEditForm->c_disabled)) {
            $coupon->c_disabled = $couponEditForm->c_disabled;
        }
        return $coupon;
    }
}
