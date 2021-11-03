<?php

namespace webapi\src\services\payment;

use common\models\CreditCard;
use common\models\PaymentMethod;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use modules\order\src\entities\order\Order;
use sales\helpers\ErrorsToStringHelper;
use sales\repositories\creditCard\CreditCardRepository;
use webapi\src\forms\payment\PaymentRequestForm;

/**
 * Class PaymentRequestVoluntaryService
 *
 * @property CreditCardRepository $creditCardRepository
 * @property InvoiceRepository $invoiceRepository;
 *
 * @property PaymentMethod|null $paymentMethod
 * @property Invoice|null $invoice
 * @property CreditCard|null $creditCard
 */
class PaymentRequestVoluntaryService
{
    private CreditCardRepository $creditCardRepository;
    private InvoiceRepository $invoiceRepository;

    private ?PaymentMethod $paymentMethod;
    private ?Invoice $invoice;
    private ?CreditCard $creditCard;

    /**
     * @param CreditCardRepository $creditCardRepository
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(
        CreditCardRepository $creditCardRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->creditCardRepository = $creditCardRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function processing(PaymentRequestForm $paymentRequestForm, ?Order $order, ?string $description): bool
    {
        if (!$paymentMethod = PaymentMethod::findOne(['pm_key' => $paymentRequestForm->method_key])) {
            throw new \RuntimeException('PaymentMethod not found by short name (' . $paymentRequestForm->method_key . ')');
        }
        $this->paymentMethod = $paymentMethod;

        $invoice = Invoice::create(
            $order ? $order->getId() : null,
            (float) $paymentRequestForm->amount,
            $paymentRequestForm->currency,
            $description,
            null
        );
        if (!$invoice->validate()) {
            throw new \RuntimeException('Invoice not saved. ' . ErrorsToStringHelper::extractFromModel($invoice));
        }
        $this->invoiceRepository->save($invoice);
        $this->invoice = $invoice;

        if ($creditCardForm = $paymentRequestForm->getCreditCardForm()) {
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
            $this->creditCardRepository->save($creditCard);
            $this->creditCard = $creditCard;
        }
        return true;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
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
