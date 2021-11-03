<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\validators\CheckJsonValidator;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use yii\base\Model;

/**
 * Class VoluntaryRefundCreateForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property $bookingId
 * @property $payment_request
 * @property $refund
 * @property $billing
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 * @property VoluntaryRefundForm $refundForm
 */
class VoluntaryRefundCreateForm extends Model
{
    public $bookingId;
    public $payment_request;
    public $billing;
    public $refund;

    private ?PaymentRequestForm $paymentRequestForm = null;
    private ?BillingInfoForm $billingInfoForm = null;
    private ?VoluntaryRefundForm $refundForm = null;

    public function rules(): array
    {
        return [
            [['bookingId'], 'required'],
            [['bookingId'], 'string', 'max' => 50],

            [['payment_request'], 'safe'],
            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['payment_request'], 'paymentRequestProcessing', 'skipOnEmpty' => false],

            [['billing'], 'safe'],
            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['billing'], 'billingProcessing', 'skipOnEmpty' => false],

            [['refund'], 'safe'],
            [['refund'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['refund'], 'refundProcessing', 'skipOnEmpty' => false],
        ];
    }

    public function paymentRequestProcessing(string $attribute): bool
    {
        if ($this->payment_request !== null) {
            $paymentRequestForm = new PaymentRequestForm();
            $paymentRequestForm->setFormName('');
            $paymentRequestForm->load($this->payment_request, '');
            if (!$paymentRequestForm->validate()) {
                $this->addError($attribute, 'PaymentRequest: ' . ErrorsToStringHelper::extractFromModel($paymentRequestForm, ', '));
            } else {
                $this->paymentRequestForm = $paymentRequestForm;
            }
        }
        return true;
    }

    public function billingProcessing(string $attribute): bool
    {
        if ($this->billing !== null) {
            $billingInfoForm = new BillingInfoForm();
            $billingInfoForm->setFormName('');
            $billingInfoForm->load($this->billing, '');
            if (!$billingInfoForm->validate()) {
                $this->addError($attribute, 'Billing: ' . ErrorsToStringHelper::extractFromModel($billingInfoForm, ', '));
            } else {
                $this->billingInfoForm = $billingInfoForm;
            }
        }
        return true;
    }

    public function refundProcessing(string $attribute): bool
    {
        $refundForm = new VoluntaryRefundForm();
        if ($this->refund === null) {
            $this->addError($attribute, 'Refund data not provided');
            return false;
        }
        $refundForm->load($this->refund);
        if (!$refundForm->validate()) {
            $this->addError($attribute, 'Refund: ' . ErrorsToStringHelper::extractFromModel($refundForm, ', '));
            return false;
        }

        $this->refundForm = $refundForm;
        return true;
    }

    public function formName(): string
    {
        return '';
    }

    public function getFilteredData(): array
    {
        $filter = \Yii::createObject(CreditCardFilter::class);
        return $filter->filterData($this->toArray());
    }

    public function getPaymentRequestForm(): ?PaymentRequestForm
    {
        return $this->paymentRequestForm;
    }

    public function getBillingInfoForm(): ?BillingInfoForm
    {
        return $this->billingInfoForm;
    }

    public function getRefundForm(): ?VoluntaryRefundForm
    {
        return $this->refundForm;
    }
}
