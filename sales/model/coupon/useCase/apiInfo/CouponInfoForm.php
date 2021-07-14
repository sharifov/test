<?php

namespace sales\model\coupon\useCase\apiInfo;

use sales\model\coupon\entity\coupon\Coupon;
use yii\base\Model;

/**
 * Class CouponInfoForm
 * @property $code
 */
class CouponInfoForm extends Model
{
    public $code;

    public function rules(): array
    {
        return [
            ['code', 'required'],
            ['code', 'string', 'max' => 16],
            ['code', 'trim'],
            ['code', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['code' => 'c_code'], 'message' => 'Coupon not found'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
