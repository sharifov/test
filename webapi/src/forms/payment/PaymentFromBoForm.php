<?php

namespace webapi\src\forms\payment;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\src\forms\api\PaymentApiForm;
use modules\order\src\entities\order\Order;
use modules\order\src\forms\api\create\BillingInfoForm;
use modules\order\src\forms\api\create\CreditCardForm;
use sales\helpers\ErrorsToStringHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentFromBoForm
 * @property $fareId
 * @property $payments
 * @property Order $order
 * @property PaymentApiForm[] $paymentApiForms
 * @property CreditCardForm[] $creditCardForms
 * @property BillingInfoForm[] $billingInfoForms
 */
class PaymentFromBoForm extends Model
{
    public $fareId;
    public $payments;

    private $order;
    private array $paymentApiForms = [];
    private array $creditCardForms = [];
    private array $billingInfoForms = [];

    public function rules(): array
    {
        return [
            [['fareId'], 'required'],
            [['fareId'], 'string', 'max' => 255],
            [['fareId'], 'detectOrder'],

            [['payments'], 'required'],
            [['payments'], CheckJsonValidator::class],
            [['payments'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['payments'], 'checkPayments'],
        ];
    }

    public function detectOrder($attribute)
    {
        if (!$this->order = Order::findOne(['or_fare_id' => $this->fareId])) {
            $this->addError($attribute, 'Order not found by fareId(' . $this->fareId . ')');
        }
    }

    public function checkPayments($attribute): void
    {
        foreach ($this->payments as $key => $payment) {
            $paymentApiForm = new PaymentApiForm();
            if (!$paymentApiForm->load($payment)) {
                $this->addError($attribute, 'PaymentApiForm is not loaded');
                break;
            }
            if (!$paymentApiForm->validate()) {
                $this->addError($attribute, 'PaymentApiForm error: ' . ErrorsToStringHelper::extractFromModel($paymentApiForm, ' '));
                break;
            }
            $this->paymentApiForms[$key] = $paymentApiForm;

            $creditCardForm = new CreditCardForm();
            if (!$creditCardForm->load($payment)) {
                $this->addError($attribute, 'CreditCard is required');
                break;
            }
            if (!$creditCardForm->validate()) {
                $this->addError($attribute, 'CreditCardForm error: ' . ErrorsToStringHelper::extractFromModel($creditCardForm, ' '));
            }
            $this->creditCardForms[$key] = $creditCardForm;

            $billingInfoForm = new BillingInfoForm();
            if (!$billingInfoForm->load($payment)) {
                $this->addError($attribute, 'BillingInfo is required');
                break;
            }
            if (!$billingInfoForm->validate()) {
                $this->addError($attribute, 'BillingInfoForm error: ' . ErrorsToStringHelper::extractFromModel($billingInfoForm, ' '));
                break;
            }
            $this->billingInfoForms[$key] = $billingInfoForm;
        }
    }

    public function formName(): string
    {
        return '';
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return PaymentApiForm[]
     */
    public function getPaymentApiForms(): array
    {
        return $this->paymentApiForms;
    }

    /**
     * @return CreditCardForm[]
     */
    public function getCreditCardForms(): array
    {
        return $this->creditCardForms;
    }

    /**
     * @return BillingInfoForm[]
     */
    public function getBillingInfoForms(): array
    {
        return $this->billingInfoForms;
    }
}
