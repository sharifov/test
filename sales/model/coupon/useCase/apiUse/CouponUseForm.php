<?php

namespace sales\model\coupon\useCase\apiUse;

use sales\model\coupon\useCase\apiInfo\CouponInfoForm;
use yii\helpers\ArrayHelper;

/**
 * Class CouponUseForm
 *
 * @property $code
 * @property $ip
 * @property $userAgent
 */
class CouponUseForm extends CouponInfoForm
{
    public $code;
    public $ip;
    public $userAgent;

    public function rules(): array
    {
        return ArrayHelper::merge(parent::rules(), [
            ['ip', 'string', 'max' => 40],
            ['userAgent', 'string', 'max' => 500],
        ]);
    }

    public function formName(): string
    {
        return '';
    }
}
