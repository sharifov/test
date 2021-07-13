<?php

namespace sales\model\coupon\useCase\apiCreate;

use sales\helpers\app\AppHelper;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\coupon\CouponType;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CouponAoiCreateService
 */
class CouponApiCreateService
{
    public const CURRENCY_AIR_SEARCH = [
        'USD' => 'USD',
    ];

    public static function createFromApiForm(CouponCreateForm $couponCreateForm, ?string $code): Coupon
    {
        $coupon = new Coupon();
        $coupon->c_status_id = CouponStatus::NEW;
        $coupon->c_type_id = self::detectTypeCoupon($couponCreateForm->reusableCount);
        $coupon->c_code = $code ?? self::uniqCodeGenerator();
        $coupon->c_start_date = $couponCreateForm->startDate;
        $coupon->c_exp_date = $couponCreateForm->expirationDate;
        $coupon->c_amount = $couponCreateForm->amount;
        $coupon->c_currency_code = $couponCreateForm->currencyCode;
        $coupon->c_public = $couponCreateForm->public;
        $coupon->c_reusable = ($couponCreateForm->reusableCount > 1);
        $coupon->c_reusable_count = $couponCreateForm->reusableCount;
        return $coupon;
    }

    public static function uniqCodeGenerator(): string
    {
        $code = strtoupper(uniqid(substr(md5(mt_rand()), 0, 2), false));
        if (Coupon::find()->where(['c_code' => $code])->exists()) {
            return self::uniqCodeGenerator();
        }
        return $code;
    }

    public static function getCodeFromAirSearch(CouponCreateForm $couponCreateForm): ?string
    {
        if (
            in_array($couponCreateForm->currencyCode, self::CURRENCY_AIR_SEARCH, false) &&
            $amountCurrencyCode = $couponCreateForm->getAmountCurrencyCode()
        ) {
            return self::requestCodeFromAirSearch($amountCurrencyCode);
        }
        return null;
    }

    private static function detectTypeCoupon(int $reusableCount): int
    {
        if ($reusableCount > 1) {
            return CouponType::COUPON;
        }
        return CouponType::VOUCHER;
    }

    private static function requestCodeFromAirSearch(?string $amountCurrencyCode): ?string
    {
        try {
            if ($coupons = \Yii::$app->airsearch->generateCoupons(1, $amountCurrencyCode)) {
                return ArrayHelper::getValue($coupons, '0.enc_coupon');
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'CouponApiCreateService:getCodeFromAirSearch');
        }
        return null;
    }
}
