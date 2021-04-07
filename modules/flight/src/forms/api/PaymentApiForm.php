<?php

namespace modules\flight\src\forms\api;

use common\models\Currency;
use common\models\Payment;
use yii\base\Model;

/**
 * Class PaymentApiForm
 *
 * @property $pay_amount
 * @property $pay_currency
 * @property $pay_code
 * @property $pay_date
 * @property $pay_method_key
 * @property $pay_description
 * @property $pay_type
 * @property $pay_auth_id
 */
class PaymentApiForm extends Model
{
    public $pay_amount;
    public $pay_currency;
    public $pay_code;
    public $pay_date;
    public $pay_method_key;
    public $pay_description;
    public $pay_type;
    public $pay_auth_id;

    public const TYPE_AUTHORIZE = 'authorize';
    public const TYPE_CAPTURE = 'capture';
    public const TYPE_REFUND = 'refund';
    public const TYPE_VOID = 'void';

    public const TYPE_LIST = [
        self::TYPE_AUTHORIZE => 'Authorize',
        self::TYPE_CAPTURE => 'Capture',
        self::TYPE_REFUND => 'Refund',
        self::TYPE_VOID => 'Void',
    ];

    public function rules(): array
    {
        return [
            [['pay_amount', 'pay_currency', 'pay_code', 'pay_date', 'pay_auth_id', 'pay_type'], 'required'],

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

            [['pay_description'], 'string', 'max' => 255],

            [['pay_auth_id'], 'integer'],
            [['pay_auth_id'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['pay_type'], 'string'],
            [['pay_type'], 'filter', 'filter' => 'strtolower'],
            [['pay_type'], 'in', 'range' => array_keys(self::TYPE_LIST)],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
