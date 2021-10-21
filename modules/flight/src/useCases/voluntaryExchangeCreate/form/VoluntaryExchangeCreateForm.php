<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form;

use common\components\validators\CheckAndConvertToJsonValidator;
use common\models\Currency;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\TripForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\price\VoluntaryExchangePriceForm;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use yii\base\Model;

/**
 * Class VoluntaryExchangeCreateForm
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 * @property VoluntaryExchangePriceForm|null $voluntaryExchangePriceForm
 * @property TripForm[]|null $tripForms
 */
class VoluntaryExchangeCreateForm extends Model
{
    public $booking_id;
    public $flight_quote;
    public $key;
    public $paxCnt;
    public $gds;
    public $pcc;
    public $validatingCarrier;
    public $fareType;
    public $cabin;
    public $currency;
    public $passengers;

    public $prices;
    public $trips;
    public $payment_request;
    public $billing;

    private ?PaymentRequestForm $paymentRequestForm;
    private ?BillingInfoForm $billingInfoForm;
    private ?VoluntaryExchangePriceForm $voluntaryExchangePriceForm;
    private array $tripForms = [];

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['key'], 'required'],
            [['key'], 'string', 'max' => 150],

            [['paxCnt'], 'integer'],

            [['validatingCarrier'], 'integer'],

            [['paxCnt'], 'integer'],

            [['gds'], 'required'],
            [['gds'], 'string', 'max' => 2],

            [['pcc'], 'string', 'max' => 10],

            [['validatingCarrier'], 'string', 'max' => 2],

            [['fareType'], 'string', 'max' => 50],

            [['cabin'], 'string'],

            ['currency', 'required'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],

            [['prices'], 'required'],
            [['prices'], CheckAndConvertToJsonValidator::class],
            [['prices'], 'pricesProcessing'],

            [['trips'], 'required'],
            [['trips'], CheckAndConvertToJsonValidator::class],
            [['trips'], 'tripsProcessing'],

            [['payment_request'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true],
            [['payment_request'], 'paymentRequestProcessing'],

            [['billing'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true],
            [['billing'], 'billingProcessing'],

            [['passengers'], 'safe'],
        ];
    }

    public function tripsProcessing(string $attribute): void
    {
        if (!empty($this->trips)) {
            foreach ($this->trips as $key => $trip) {
                $tripForm = new TripForm();
                $tripForm->setFormName('');
                if (!$tripForm->load($trip)) {
                    $this->addError($attribute, 'TripForm not loaded');
                } elseif (!$tripForm->validate()) {
                    $this->addError($attribute, 'TripForm.' . $key . '.' . ErrorsToStringHelper::extractFromModel($tripForm, ' '));
                } else {
                    $this->tripForms[] = $tripForm;
                }
            }
        }
    }

    public function pricesProcessing(string $attribute): void
    {
        if (!empty($this->prices)) {
            $form = new VoluntaryExchangePriceForm();
            $form->setFormName('');
            if (!$form->load($this->prices)) {
                $this->addError($attribute, 'VoluntaryExchangePriceForm not loaded');
            } elseif (!$form->validate()) {
                $this->addError($attribute, 'VoluntaryExchangePriceForm: ' . ErrorsToStringHelper::extractFromModel($form, ', '));
            } else {
                $this->voluntaryExchangePriceForm = $form;
            }
        }
    }

    public function paymentRequestProcessing(string $attribute): void
    {
        if (!empty($this->payment_request)) {
            $paymentRequestForm = new PaymentRequestForm();
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

    public function getVoluntaryExchangePriceForm(): ?VoluntaryExchangePriceForm
    {
        return $this->voluntaryExchangePriceForm;
    }

    public function getTripForms(): array
    {
        return $this->tripForms;
    }
}
