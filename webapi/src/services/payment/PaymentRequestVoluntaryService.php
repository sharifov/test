<?php

namespace webapi\src\services\payment;

use common\models\CreditCard;
use common\models\PaymentMethod;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\invoice\src\entities\invoice\Invoice;
use modules\order\src\entities\order\Order;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\payment\PaymentRequestForm;

/**
 * Class PaymentRequestVoluntaryService
 *
 * @property PaymentRequestForm $paymentRequestForm
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property Order $order
 *
 * @property PaymentMethod|null $paymentMethod
 * @property Invoice|null $invoice
 * @property CreditCard|null $creditCard
 */
class PaymentRequestVoluntaryService
{
    private PaymentRequestForm $paymentRequestForm;
    private VoluntaryExchangeObjectCollection $objectCollection;
    private Order $order;

    private ?PaymentMethod $paymentMethod;
    private ?Invoice $invoice;
    private ?CreditCard $creditCard;

    /**
     * @param PaymentRequestForm $paymentRequestForm
     * @param VoluntaryExchangeObjectCollection $objectCollection
     * @param Order $order
     */
    public function __construct(
        PaymentRequestForm $paymentRequestForm,
        VoluntaryExchangeObjectCollection $objectCollection,
        Order $order
    ) {
        $this->paymentRequestForm = $paymentRequestForm;
        $this->objectCollection = $objectCollection;
        $this->order = $order;
    }

    public function processing(): bool
    {
        if (!$paymentMethod = PaymentMethod::findOne(['pm_key' => $this->paymentRequestForm->method_key])) {
            throw new \RuntimeException('PaymentMethod not found by key (' . $this->paymentRequestForm->method_key . ')');
        }
        $this->paymentMethod = $paymentMethod;

        $invoice = Invoice::create(
            $this->order->getId(),
            (float) $this->paymentRequestForm->amount,
            $this->paymentRequestForm->currency,
            'Create by Voluntary Exchange API processing',
            null
        );
        if (!$invoice->validate()) {
            throw new \RuntimeException('Invoice not saved. ' . ErrorsToStringHelper::extractFromModel($invoice));
        }
        $this->objectCollection->getInvoiceRepository()->save($invoice);
        $this->invoice = $invoice;

        if ($creditCardForm = $this->paymentRequestForm->getCreditCardForm()) {
            $creditCard = CreditCard::create(
                $creditCardForm->number,
                $creditCardForm->holder_name,
                $creditCardForm->expiration_month,
                $creditCardForm->expiration_year,
                $creditCardForm->cvv,
                null
            );
            $creditCard->updateSecureCardNumber();
            $creditCard->updateSecureCvv();
            if (!$creditCard->validate()) {
                throw new \RuntimeException('CreditCard not saved. ' . ErrorsToStringHelper::extractFromModel($creditCard));
            }
            $this->objectCollection->getCreditCardRepository()->save($creditCard);
            $this->creditCard = $creditCard;
        }
        return true;
    }

    private function creditCardProcessing()
    {

        /* TODO::  */
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function getCreditCard(): ?CreditCard
    {
        return $this->creditCard;
    }
}
