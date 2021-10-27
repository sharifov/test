<?php

namespace webapi\src\forms\payment;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use common\models\Currency;
use common\models\PaymentMethod;
use frontend\helpers\JsonHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\traits\FormNameModelTrait;
use webapi\src\forms\payment\creditCard\CreditCardForm;
use yii\base\Model;
use common\components\validators\CheckIsNumberValidator;

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

    private const REQUIRED_METHOD_DATA_BY_TYPES = [
        self::TYPE_METHOD_CARD
    ];

    public function rules(): array
    {
        return [
            [['amount'], 'required'],
            [['amount'], CheckIsNumberValidator::class, 'allowInt' => true],

            [['method_key'], 'required'],
            [['method_key'], 'string', 'max' => 50],
            [['method_key'], 'exist', 'skipOnError' => true,
                'targetClass' => PaymentMethod::class, 'targetAttribute' => ['method_key' => 'pm_key']],

            [['currency'], 'required'],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'filter', 'filter' => 'strtoupper'],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['method_data'], 'required', 'when' => static function (self $model) {
                return in_array($model->method_key, self::REQUIRED_METHOD_DATA_BY_TYPES, true);
            }],
            [['method_data'], IsArrayValidator::class, 'skipOnEmpty' => true],
            [['method_data'], 'methodDataProcessing'],
        ];
    }

    public function methodDataProcessing(string $attribute): bool
    {
        if (empty($this->method_data[$this->method_key]) && in_array($this->method_key, self::REQUIRED_METHOD_DATA_BY_TYPES, true)) {
            $this->addError($attribute, $this->method_key . ' data is not provided');
            return false;
        }

        if (!empty($this->method_data[$this->method_key]) && is_array($this->method_data[$this->method_key])) {
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
