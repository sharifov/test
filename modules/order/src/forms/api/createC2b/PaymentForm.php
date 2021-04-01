<?php

namespace modules\order\src\forms\api\createC2b;

use common\models\Currency;
use common\models\PaymentMethod;

/**
 * Class PaymentForm
 * @package modules\order\src\forms\api
 *
 * @property string $clientCurrency
 */
class PaymentForm extends \yii\base\Model
{
    public $clientCurrency;

    public function rules(): array
    {
        return [
            ['clientCurrency', 'string', 'max' => 3],
            ['clientCurrency', 'exist', 'skipOnEmpty' => true, 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],
        ];
    }

    public function beforeValidate(): bool
    {
        if (empty($this->clientCurrency)) {
            $this->clientCurrency = Currency::getDefaultCurrencyCode();
        }
        return parent::beforeValidate();
    }

    public function formName(): string
    {
        return 'payment';
    }
}
