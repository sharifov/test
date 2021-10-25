<?php

namespace modules\flight\src\useCases\api\voluntaryRefundConfirm;

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
 * @property $bookingId
 * @property $payment_request
 * @property $refund
 * @property $billing
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 */
class VoluntaryRefundConfirmForm extends Model
{
    public $bookingId;
    public $payment_request;
    public $billing;

    public ?PaymentRequestForm $paymentRequestForm = null;
    public ?BillingInfoForm $billingInfoForm = null;

    public function rules(): array
    {
        return [
            [['bookingId'], 'required'],
            [['bookingId'], 'string', 'max' => 10],

            [['payment_request'], 'safe'],
            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['payment_request'], 'paymentRequestProcessing', 'skipOnEmpty' => false],

            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['billing'], 'billingProcessing', 'skipOnEmpty' => false],
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

    public function formName(): string
    {
        return '';
    }
}
