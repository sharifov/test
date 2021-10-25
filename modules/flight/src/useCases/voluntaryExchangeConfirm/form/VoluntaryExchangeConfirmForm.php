<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\form;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use yii\base\Model;

/**
 * Class VoluntaryExchangeConfirmForm
 *
 * @property $booking_id
 * @property $payment_request
 * @property $billing
 * @property $change_gid
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 */
class VoluntaryExchangeConfirmForm extends Model
{
    public $booking_id;
    public $payment_request;
    public $billing;
    public $change_gid;

    private ?PaymentRequestForm $paymentRequestForm;
    private ?BillingInfoForm $billingInfoForm;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['change_gid'], 'required'],
            [['change_gid'], 'string', 'max' => 32],
            [['change_gid'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteChange::class, 'targetAttribute' => ['change_gid' => 'pqc_gid']],

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
        if (!empty($this->billing)) {
            $billingInfoForm = new BillingInfoForm();
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
}
