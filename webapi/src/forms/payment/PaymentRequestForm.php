<?php

namespace webapi\src\forms\payment;

use common\components\validators\CheckJsonValidator;
use common\models\Currency;
use common\models\PaymentMethod;
use frontend\helpers\JsonHelper;
use modules\order\src\forms\api\create\CreditCardForm;
use sales\helpers\ErrorsToStringHelper;
use sales\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class PaymentRequestForm
 *
 * @property $method_key
 * @property $currency
 * @property $method_data
 *
 * @property CreditCardForm[] $creditCardForms
 */
class PaymentRequestForm extends Model
{
    use FormNameModelTrait;

    public const TYPE_METHOD_CARD = 'card';

    public $method_key;
    public $currency;
    public $method_data;

    private array $creditCardForms = [];

    public function rules(): array
    {
        return [
            [['method_key'], 'required'],
            [['method_key'], 'string', 'max' => 2],
            [['method_key'], 'filter', 'filter' => 'strtoupper'],
            [['method_key'], 'exist', 'skipOnError' => true,
                'targetClass' => PaymentMethod::class, 'targetAttribute' => ['method_key' => 'pm_short_name']],

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

    public function methodDataProcessing($attribute)
    {
        if (!empty($this->method_data[self::TYPE_METHOD_CARD]) && is_array($this->method_data[self::TYPE_METHOD_CARD])) {
            foreach ($this->method_data[self::TYPE_METHOD_CARD] as $key => $card) {
                $creditCardForm = new CreditCardForm();
                if (!$creditCardForm->load($card)) {
                    $this->addError($attribute, 'CreditCardForm is not loaded');
                    break;
                }
                if (!$creditCardForm->validate()) {
                    $this->addError($attribute, 'BookingInfoApiForm: ' . ErrorsToStringHelper::extractFromModel($creditCardForm, ', '));
                    break;
                }
                $this->creditCardForms[$key] = $creditCardForm;
            }
        }
    }

    /**
     * @return CreditCardForm[]
     */
    public function getCreditCardForms(): array
    {
        return $this->creditCardForms;
    }
}
