<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\validators\CheckJsonValidator;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryRefundCreateForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property $booking_id
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
    public $booking_id;
    public $payment_request;
    public $billing;
    public $refund;

    public ?PaymentRequestForm $paymentRequestForm = null;
    public ?BillingInfoForm $billingInfoForm = null;
    public ?VoluntaryRefundForm $refundForm = null;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

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
}
