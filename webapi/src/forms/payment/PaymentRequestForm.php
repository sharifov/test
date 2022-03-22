<?php

namespace webapi\src\forms\payment;

use common\components\validators\IsArrayValidator;
use common\models\Currency;
use common\models\PaymentMethod;
use src\helpers\ErrorsToStringHelper;
use src\traits\FormNameModelTrait;
use webapi\src\forms\payment\creditCard\CreditCardForm;
use webapi\src\forms\payment\creditCard\StripeForm;
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
 * @property StripeForm $stripeForm
 */
class PaymentRequestForm extends Model
{
    use FormNameModelTrait;

    public const SCENARIO_WITHOUT_PRIVATE_DATA = \webapi\src\forms\payment\creditCard\CreditCardForm::SCENARIO_WITHOUT_PRIVATE_DATA;

    public const TYPE_METHOD_CARD = 'card';
    public const TYPE_METHOD_STRIPE = 'stripe';

    public $method_key;
    public $currency;
    public $method_data;
    public $amount;

    private ?CreditCardForm $creditCardForm = null;
    private ?StripeForm $stripeForm = null;

    private const REQUIRED_METHOD_DATA_BY_TYPES = [
        self::TYPE_METHOD_CARD,
        self::TYPE_METHOD_STRIPE,
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

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_WITHOUT_PRIVATE_DATA] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    public function methodDataProcessing(string $attribute): bool
    {
        if (empty($this->method_data[$this->method_key]) && in_array($this->method_key, self::REQUIRED_METHOD_DATA_BY_TYPES, true)) {
            $this->addError($attribute, $this->method_key . ' data is not provided');
        } else if (!empty($this->method_data[$this->method_key]) && is_array($this->method_data[$this->method_key])) {
            switch ($this->method_key) {
                case self::TYPE_METHOD_CARD:
                    $creditCardForm = new CreditCardForm([
                        'scenario' => $this->scenario,
                    ]);
                    $creditCardForm->load($this->method_data, self::TYPE_METHOD_CARD);
                    if (!$creditCardForm->validate()) {
                        $this->addError($attribute, 'CreditCardForm: ' . ErrorsToStringHelper::extractFromModel($creditCardForm, ', '));
                    }
                    $this->creditCardForm = $creditCardForm;
                    break;
                case self::TYPE_METHOD_STRIPE:
                    $stripeForm = new StripeForm();
                    $stripeForm->load($this->method_data, self::TYPE_METHOD_STRIPE);
                    if (!$stripeForm->validate()) {
                        $this->addError($attribute, 'StripeForm: ' . ErrorsToStringHelper::extractFromModel($stripeForm, ', '));
                    }
                    $this->stripeForm = $stripeForm;
                    break;
            }
        }
        return !$this->hasErrors();
    }

    public function getCreditCardForm(): ?CreditCardForm
    {
        return $this->creditCardForm;
    }

    public function getStripeForm(): ?StripeForm
    {
        return $this->stripeForm;
    }
}
