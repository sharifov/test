<?php

namespace sales\model\coupon\useCase\apiUse;

use sales\model\coupon\entity\coupon\Coupon;
use yii\base\Model;

/**
 * Class CouponUseForm
 *
 * @property $code
 * @property $clientIp
 * @property $clientUserAgent
 */
class CouponUseForm extends Model
{
    public $code;
    public $clientIp;
    public $clientUserAgent;

    public function rules(): array
    {
        return [
            ['code', 'required'],
            ['code', 'string', 'min' => 14, 'max' => 16],
            ['code', 'trim'],
            ['code', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['code' => 'c_code'], 'message' => 'Coupon not found'],

            ['clientIp', 'string', 'max' => 40],

            ['clientUserAgent', 'string', 'max' => 500],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
