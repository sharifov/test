<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use sales\forms\CompositeRecursiveForm;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;

/**
 * Class VoluntaryRefundCreateForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $booking_id
 * @property BillingInfoForm|null $billing
 * @property PaymentRequestForm|null $payment_request
 * @property VoluntaryRefundForm $refund
 */
class VoluntaryRefundCreateForm extends CompositeRecursiveForm
{
    public $booking_id;

    public function __construct($config = [])
    {
        $this->refund = new VoluntaryRefundForm();
        parent::__construct($config);
    }

    public function load($data, $formName = null, $forms = [])
    {
        if (isset($data['billing'])) {
            $this->billing = new BillingInfoForm();
            $this->billing->setFormName('billing');
        }

        if (isset($data['payment_request'])) {
            $this->payment_request = new PaymentRequestForm();
            $this->payment_request->setFormName('payment_request');
        }

        return parent::load($data, $formName, $forms); // TODO: Change the autogenerated stub
    }

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['refund', 'billing', 'payment_request'];
    }
}
