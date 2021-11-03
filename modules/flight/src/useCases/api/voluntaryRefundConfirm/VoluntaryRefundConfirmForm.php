<?php

namespace modules\flight\src\useCases\api\voluntaryRefundConfirm;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryRefundCreateForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property $bookingId
 * @property $refundGid
 * @property $payment_request
 * @property $billing
 * @property $orderId
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 */
class VoluntaryRefundConfirmForm extends Model
{
    public $bookingId;
    public $payment_request;
    public $billing;
    public $refundGid;
    public $orderId;

    private ?PaymentRequestForm $paymentRequestForm = null;
    private ?BillingInfoForm $billingInfoForm = null;

    public function rules(): array
    {
        return [
            [['bookingId', 'refundGid', 'orderId'], 'required'],
            [['bookingId'], 'string', 'max' => 10],
            [['refundGid'], 'string', 'max' => 32],
            [['orderId'], 'string'],

            [['payment_request'], 'safe'],
            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['payment_request'], 'paymentRequestProcessing', 'skipOnEmpty' => false],

            [['billing'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['billing'], 'billingProcessing', 'skipOnEmpty' => false],

            [['refundGid'], 'exist', 'targetClass' => ProductQuoteRefund::class, 'targetAttribute' => ['refundGid' => 'pqr_gid'], 'skipOnError' => true, 'message' => 'Refund not found by gid']
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
}
