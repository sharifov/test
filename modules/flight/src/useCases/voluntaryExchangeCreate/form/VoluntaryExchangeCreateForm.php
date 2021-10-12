<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\FlightQuoteForm;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use yii\base\Model;

/**
 * Class VoluntaryExchangeCreateForm
 *
 * @property $booking_id
 * @property $flight_quote
 * @property $payment_request
 * @property $billing
 * @property $is_automate
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 */
class VoluntaryExchangeCreateForm extends Model
{
    public $booking_id;
    public $flight_quote;
    public $payment_request;
    public $billing;
    public $is_automate;

    private ?PaymentRequestForm $paymentRequestForm;
    private ?BillingInfoForm $billingInfoForm;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['is_automate'], 'boolean', 'strict' => true, 'trueValue' => true, 'falseValue' => false, 'skipOnEmpty' => true],
            [['is_automate'], 'default', 'value' => false],

            [['flight_quote'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['flight_quote'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['flight_quote'], 'checkFlightQuoteForm'],

            [['payment_request'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['payment_request'], 'paymentRequestProcessing'],

            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['billing'], 'billingProcessing'],

            [['booking_id'], 'checkExistByHash'],
        ];
    }

    public function checkFlightQuoteForm($attribute)
    {
        if (!empty($this->flight_quote)) {
            $flightQuoteForm = new FlightQuoteForm();
            if (!$flightQuoteForm->load($this->flight_quote)) {
                $this->addError($attribute, 'FlightQuoteForm not loaded');
            } elseif (!$flightQuoteForm->validate()) {
                $this->addError($attribute, 'FlightQuoteForm: ' . ErrorsToStringHelper::extractFromModel($flightQuoteForm, ' '));
            } else {
                $this->flightQuoteForm = $flightQuoteForm;
            }
        }
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

    public function checkExistByHash($attribute)
    {
        $hash = FlightRequest::generateHashFromDataJson($this->getAttributes());
        if (FlightRequest::findOne(['fr_hash' => $hash])) {
            $this->addError($attribute, 'FlightRequest already exist. Hash(' . $hash . ')');
        }
    }

    public function formName(): string
    {
        return '';
    }
}
