<?php

namespace webapi\src\forms\payment;

use common\components\validators\CheckJsonValidator;
use common\models\Currency;
use common\models\PaymentMethod;
use frontend\helpers\JsonHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\traits\FormNameModelTrait;
use webapi\src\forms\payment\creditCard\CreditCardForm;
use yii\base\Model;

/**
 * Class PaymentRequestForm
 *
 * @property $method_key
 * @property $currency
 * @property $method_data
 * @property $amount
 *
 * @property CreditCardForm $creditCardForm
 */
class PaymentRequestForm extends Model
{
    use FormNameModelTrait;

    public const TYPE_METHOD_CARD = 'card';

    public $method_key;
    public $currency;
    public $method_data;
    public $amount;

    private ?CreditCardForm $creditCardForm = null;

    public function rules(): array
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'number'],

            [['method_key'], 'required'],
            [['method_key'], 'string', 'max' => 50],
            [['method_key'], 'filter', 'filter' => 'strtoupper'],
            [['method_key'], 'exist', 'skipOnError' => true,
                'targetClass' => PaymentMethod::class, 'targetAttribute' => ['method_key' => 'pm_key']],

            [['currency'], 'required'],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'filter', 'filter' => 'strtoupper'],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['method_data'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['method_data'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['method_data'], 'methodDataProcessing'],
        ];
    }

    public function methodDataProcessing(string $attribute): bool
    {
        if (!empty($this->method_data[self::TYPE_METHOD_CARD]) && is_array($this->method_data[self::TYPE_METHOD_CARD])) {
            $creditCardForm = new CreditCardForm();
            $creditCardForm->load($this->method_data, self::TYPE_METHOD_CARD);
            if (!$creditCardForm->validate()) {
                $this->addError($attribute, 'CreditCardForm: ' . ErrorsToStringHelper::extractFromModel($creditCardForm, ', '));
                return false;
            }
            $this->creditCardForm = $creditCardForm;
        }
        return true;
    }

    public function getCreditCardForm(): ?CreditCardForm
    {
        return $this->creditCardForm;
    }
}
