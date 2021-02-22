<?php

namespace sales\model\coupon\useCase\request;

use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\coupon\CouponType;
use sales\model\coupon\entity\couponCase\CouponCase;

/**
 * Class RequestCouponService
 *
 * @property array $errors
 */
class RequestCouponService
{
    private $errors = [];

    public function request(RequestForm $form): array
    {
        if (!$coupons = \Yii::$app->airsearch->generateCoupons($form->count, $form->code)) {
            throw new \DomainException('Could not generate a coupon. No coupons available for this value.');
        }
        foreach ($coupons as $item) {
            $coupon = new CouponForm();
            $coupon->load($item);
            if (!$coupon->validate()) {
                $this->addError(['model' => $coupon->getAttributes(), 'errors' => $coupon->getErrors()]);
                continue;
            }
            $this->saveCoupon($form, $coupon);
        }
        return $this->errors;
    }

    private function saveCoupon(RequestForm $form, CouponForm $couponForm): void
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $coupon = new Coupon();
        $coupon->c_status_id = CouponStatus::NEW;
        $coupon->c_type_id = CouponType::VOUCHER;
        $coupon->c_code = $couponForm->enc_coupon;
        $coupon->c_exp_date = $couponForm->exp_date;
        $coupon->c_amount = $couponForm->amount;
        $coupon->c_currency_code = $couponForm->currency;
        $coupon->c_public = $couponForm->public;
        $coupon->c_reusable = $couponForm->reusable;
        if (!$coupon->save()) {
            $this->addError(['model' => $coupon->getAttributes(), 'errors' => $coupon->getErrors()]);
            $transaction->rollBack();
            return;
        }
        $couponCase = new CouponCase();
        $couponCase->cc_coupon_id = $coupon->c_id;
        $couponCase->cc_case_id = $form->caseId;
        if (!$couponCase->save()) {
            $this->addError(['model' => $couponCase->getAttributes(), 'errors' => $couponCase->getErrors()]);
            $transaction->rollBack();
            return;
        }
        $transaction->commit();
    }

    private function addError($error): void
    {
        $this->errors[] = $error;
    }
}
