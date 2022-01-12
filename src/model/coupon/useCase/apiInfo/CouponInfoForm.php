<?php

namespace src\model\coupon\useCase\apiInfo;

use src\model\coupon\entity\coupon\Coupon;
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
            ['code', 'string', 'min' => 14, 'max' => 16],
            ['code', 'trim'],
            ['code', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['code' => 'c_code'], 'message' => 'Coupon not found'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
