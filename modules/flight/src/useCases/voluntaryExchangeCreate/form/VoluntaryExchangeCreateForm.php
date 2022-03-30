<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form;

use common\components\validators\CheckAndConvertToJsonValidator;
use common\models\Currency;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\exchange\ExchangeForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\TripForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\price\VoluntaryExchangePriceForm;
use src\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use yii\base\Model;

/**
 * Class VoluntaryExchangeCreateForm
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 * @property ExchangeForm|null $exchangeForm
 */
class VoluntaryExchangeCreateForm extends Model
{
    public const SCENARIO_WITHOUT_PRIVATE_DATA = \webapi\src\forms\payment\creditCard\CreditCardForm::SCENARIO_WITHOUT_PRIVATE_DATA;

    public $bookingId;
    public $apiKey;
    public $exchange;

    public $payment_request;
    public $billing;

    private ?PaymentRequestForm $paymentRequestForm = null;
    private ?BillingInfoForm $billingInfoForm = null;
    private ?ExchangeForm $exchangeForm = null;

    public function rules(): array
    {
        return [
            [['bookingId'], 'required'],
            [['bookingId'], 'string', 'max' => 10],

            [['apiKey'], 'required'],
            [['apiKey'], 'string', 'max' => 150],

            [['exchange'], 'required'],
            [['exchange'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            [['exchange'], 'exchangeProcessing'],

            [['payment_request'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            [['payment_request'], 'paymentRequestProcessing'],

            [['billing'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            [['billing'], 'billingProcessing'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_WITHOUT_PRIVATE_DATA] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    public function exchangeProcessing(string $attribute): void
    {
        if (!empty($this->exchange)) {
            $exchangeForm = new ExchangeForm();
            $exchangeForm->setFormName('');
            if (!$exchangeForm->load($this->exchange)) {
                $this->addError($attribute, 'ExchangeForm is not loaded');
            } elseif (!$exchangeForm->validate()) {
                $this->addError($attribute, 'ExchangeForm: ' . ErrorsToStringHelper::extractFromModel($exchangeForm, ', '));
            } else {
                $this->exchangeForm = $exchangeForm;
            }
        }
    }

    public function paymentRequestProcessing(string $attribute): void
    {
        if (!empty($this->payment_request)) {
            $paymentRequestForm = new PaymentRequestForm([
                'scenario' => $this->scenario,
            ]);
            $paymentRequestForm->setFormName('');
            if (!$paymentRequestForm->load($this->payment_request)) {
                $this->addError($attribute, 'PaymentRequestForm is not loaded');
            } elseif (!$paymentRequestForm->validate()) {
                $this->addError($attribute, 'PaymentRequestForm: ' . ErrorsToStringHelper::extractFromModel($paymentRequestForm, ', '));
            } else {
                $this->paymentRequestForm = $paymentRequestForm;
            }
        }
    }

    public function billingProcessing(string $attribute): void
    {
        if (!empty($this->billing)) {
            $billingInfoForm = new BillingInfoForm();
            $billingInfoForm->setFormName('');
            if (!$billingInfoForm->load($this->billing)) {
                $this->addError($attribute, 'BillingInfoForm is not loaded');
            } elseif (!$billingInfoForm->validate()) {
                $this->addError($attribute, 'BillingInfoForm: ' . ErrorsToStringHelper::extractFromModel($billingInfoForm, ', '));
            } else {
                $this->billingInfoForm = $billingInfoForm;
            }
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getPaymentRequestForm(): ?PaymentRequestForm
    {
        return $this->paymentRequestForm;
    }

    public function getBillingInfoForm(): ?BillingInfoForm
    {
        return $this->billingInfoForm;
    }

    public function getFilteredData(): array
    {
        return (\Yii::createObject(CreditCardFilter::class))->filterData($this->toArray());
    }
}
