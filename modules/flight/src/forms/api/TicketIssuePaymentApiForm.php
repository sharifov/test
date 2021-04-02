<?php

namespace modules\flight\src\forms\api;

use common\models\Currency;
use common\models\PaymentMethod;
use yii\base\Model;

/**
 * Class TicketIssuePaymentApiForm
 *
 * @property $pay_amount
 * @property $pay_currency
 * @property $pay_code
 * @property $pay_date
 * @property $pay_method_key
 * @property $pay_description
 */
class TicketIssuePaymentApiForm extends Model
{
    public $pay_amount;
    public $pay_currency;
    public $pay_code;
    public $pay_date;
    public $pay_method_key;
    public $pay_description;

    public function rules(): array
    {
        return [
            [['pay_amount', 'pay_currency', 'pay_code', 'pay_date'], 'required'],

            [['pay_amount'], 'number'],
            [['pay_amount'], 'filter', 'filter' => static function ($value) {
                return (float) $value;
            }],

            [['pay_currency'], 'filter', 'filter' => 'trim'],
            [['pay_currency'], 'filter', 'filter' => 'strtoupper'],
            [['pay_currency'], 'string', 'max' => 3],
            [['pay_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pay_currency' => 'cur_code']],

            [['pay_code'], 'string', 'max' => 255],

            [['pay_date'], 'date', 'format' => 'php:Y-m-d'],

            [['pay_method_key'], 'default', 'value' => 'card'],
            [['pay_method_key'], 'string', 'max' => 50],
            [['pay_method_key'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['pay_method_key' => 'pm_key']],

            [['pay_description'], 'string', 'max' => 500],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
