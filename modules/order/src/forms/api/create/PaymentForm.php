<?php

namespace modules\order\src\forms\api\create;

use common\models\Currency;
use common\models\PaymentMethod;

/**
 * Class PaymentForm
 * @package modules\order\src\forms\api
 *
 * @property string $type
 * @property string $transactionId
 * @property string $currency
 * @property string $date
 * @property string $amount
 */
class PaymentForm extends \yii\base\Model
{
    public $type;

    public $transactionId;

    public $date;

    public $amount;

    public $currency;

    public function rules(): array
    {
        return [
            [['type', 'transactionId', 'date', 'amount', 'currency'], 'required'],
            ['type', 'exist', 'targetClass' => PaymentMethod::class, 'targetAttribute' => 'pm_key'],
            ['transactionId', 'string', 'max' => 255],
            ['date', 'date', 'format' => 'php:Y-m-d'],
            ['amount', 'filter', 'filter' => 'floatval'],
            ['type', 'filter', 'filter' => 'mb_strtolower'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code']

        ];
    }

    public function formName(): string
    {
        return 'payment';
    }
}
