<?php

namespace sales\model\coupon\useCase\apiCreate;

use sales\helpers\app\AppHelper;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\coupon\CouponType;
use sales\model\coupon\useCase\request\CouponForm;
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
        $coupon->c_type_id = self::detectTypeCoupon($couponCreateForm->reusable);
        $coupon->c_code = $code ?? self::uniqCodeGenerator();
        $coupon->c_start_date = $couponCreateForm->startDate;
        $coupon->c_exp_date = $couponCreateForm->expirationDate;
        $coupon->c_amount = $couponCreateForm->amount;
        $coupon->c_currency_code = $couponCreateForm->currencyCode;
        $coupon->c_public = $couponCreateForm->public;
        $coupon->c_reusable = $couponCreateForm->reusable;
        $coupon->c_reusable_count = $couponCreateForm->reusableCount;
        $coupon->c_percent = $couponCreateForm->percent;
        return $coupon;
    }

    public static function createFromAirSearch(CouponCreateForm $couponCreateForm, CouponForm $couponForm): Coupon
    {
        $coupon = new Coupon();
        $coupon->c_status_id = CouponStatus::NEW;
        $coupon->c_type_id = self::detectTypeCoupon($couponForm->reusable);
        $coupon->c_code = $couponForm->enc_coupon;
        $coupon->c_start_date = $couponCreateForm->startDate;
        $coupon->c_exp_date = date('Y-m-d 23:59:59', strtotime($couponForm->exp_date));
        $coupon->c_amount = $couponForm->amount;
        $coupon->c_currency_code = $couponForm->currency;
        $coupon->c_public = $couponForm->public;
        $coupon->c_reusable = $couponForm->reusable;
        $coupon->c_reusable_count = $couponCreateForm->reusableCount;
        $coupon->c_percent = $couponCreateForm->percent;
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

    private static function detectTypeCoupon(bool $reusable): int
    {
        return ($reusable) ? CouponType::COUPON : CouponType::VOUCHER;
    }

    private static function requestCodeFromAirSearch(?string $amountCurrencyCode): ?string
    {
        try {
            if ($coupons = \Yii::$app->airsearch->generateCoupons(1, $amountCurrencyCode)) {
                return ArrayHelper::getValue($coupons, '0.enc_coupon');
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'CouponApiCreateService:requestCodeFromAirSearch');
        }
        return null;
    }

    public static function requestCouponFromAirSearch(?string $amountCurrencyCode): array
    {
        try {
            $response = \Yii::$app->airsearch->apiGenerateCoupons(1, $amountCurrencyCode);

            if ($response['error']) {
                throw new \RuntimeException($response['error']);
            }
            if (!$couponSource = ArrayHelper::getValue($response['data'], '0')) {
                throw new \RuntimeException('Service did not generate coupon');
            }
            return $couponSource;
        } catch (\Throwable $throwable) {
            throw new \DomainException($throwable->getMessage());
        }
    }
}
