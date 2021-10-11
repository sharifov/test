<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use yii\base\Model;

/**
 * Class VoluntaryExchangeInfoForm
 *
 * @property $booking_id
 * @property $flight_product_quote
 * @property $payment_request
 * @property $billing
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 */
class VoluntaryExchangeCreateForm extends Model
{
    public $booking_id;
    public $flight_product_quote;
    public $payment_request;
    public $billing;

    private ?PaymentRequestForm $paymentRequestForm;
    private ?BillingInfoForm $billingInfoForm;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['flight_product_quote'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['flight_product_quote'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],

            [['payment_request'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['payment_request'], 'paymentRequestProcessing'],

            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['billing'], 'billingProcessing'],
        ];
    }

    public function paymentRequestProcessing(string $attribute): void
    {
        if (!empty($this->payment_request)) {
            $paymentRequestForm = new PaymentRequestForm();
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
        if (!empty($this->payment_request)) {
            $billingInfoForm = new BillingInfoForm();
            if (!$billingInfoForm->load($this->payment_request)) {
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
}