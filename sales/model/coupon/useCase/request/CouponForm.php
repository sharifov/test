<?php

namespace sales\model\coupon\useCase\request;

use yii\base\Model;

/**
 * Class CouponForm
 *
 * @property $enc_coupon
 * @property $create_date
 * @property $exp_date
 * @property $amount
 * @property $currency
 * @property $public
 * @property $reusable
 */
class CouponForm extends Model
{
    public const CURRENCY_USD = 'USD';

    public const CURRENCY_LIST = [
        self::CURRENCY_USD => self::CURRENCY_USD,
    ];

    public $enc_coupon;
    public $create_date;
    public $exp_date;
    public $amount;
    public $currency;
    public $public;
    public $reusable;

    public function rules(): array
    {
        return [
            ['enc_coupon', 'required'],
            ['enc_coupon', 'string', 'max' => 20],

            [['exp_date', 'create_date'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d H:i:s', strtotime($value));
            }, 'skipOnEmpty' => true],
            [['exp_date', 'create_date'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['amount', 'required'],
            ['amount', 'integer'],

            ['currency', 'required'],
            ['currency', 'in', 'range' => array_keys(self::CURRENCY_LIST)],

            ['public', 'boolean'],

            ['reusable', 'boolean'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
