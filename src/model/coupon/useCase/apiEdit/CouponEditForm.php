<?php

namespace src\model\coupon\useCase\apiEdit;

use src\model\coupon\entity\coupon\Coupon;
use yii\base\Model;

/**
 * Class CouponEditForm
 * @property $code
 * @property $c_start_date
 * @property $c_exp_date
 * @property $c_public
 * @property $c_disabled
 */
class CouponEditForm extends Model
{
    public $code;
    public $c_start_date;
    public $c_exp_date;
    public $c_public;
    public $c_disabled;

    public function rules(): array
    {
        return [
            ['code', 'required'],
            ['code', 'string', 'min' => 14, 'max' => 16],
            ['code', 'trim'],
            ['code', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['code' => 'c_code'], 'message' => 'Coupon not found'],

            [['c_start_date', 'c_exp_date'], 'date', 'format' => 'php:Y-m-d'],

            [['c_start_date'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d 00:00:00', strtotime($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['c_exp_date'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d 23:59:59', strtotime($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['c_exp_date'], 'checkDate'],

            [['c_disabled', 'c_public'], 'boolean'],
            [['c_disabled', 'c_public'], 'filter', 'filter' => 'boolval', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function checkDate($attribute): void
    {
        if ($this->c_start_date && $this->c_exp_date && ($this->c_start_date > $this->c_exp_date)) {
            $this->addError($attribute, 'Expiration Date must be older Start Date');
        }
    }

    public function formName(): string
    {
        return '';
    }
}
