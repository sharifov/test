<?php

namespace webapi\src\services\payment;

use modules\invoice\src\entities\invoice\InvoiceRepository;
use modules\order\src\forms\api\create\BillingInfoForm;
use modules\order\src\forms\api\create\CreditCardForm;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\transaction\repository\TransactionRepository;
use src\repositories\billingInfo\BillingInfoRepository;
use src\repositories\creditCard\CreditCardRepository;
use src\services\TransactionManager;
use webapi\src\forms\payment\PaymentFromBoForm;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentManageApiService
 *
 * @property TransactionManager $transactionManager
 * @property PaymentRepository $paymentRepository
 * @property TransactionRepository $transactionRepository
 * @property CreditCardRepository $creditCardRepository
 * @property BillingInfoRepository $billingInfoRepository
 * @property InvoiceRepository $invoiceRepository
 */
class PaymentManageApiService
{
    private PaymentRepository $paymentRepository;
    private TransactionManager $transactionManager;
    private TransactionRepository $transactionRepository;
    private CreditCardRepository $creditCardRepository;
    private BillingInfoRepository $billingInfoRepository;
    private InvoiceRepository $invoiceRepository;

    /**
     * @param TransactionManager $transactionManager
     * @param PaymentRepository $paymentRepository
     * @param TransactionRepository $transactionRepository
     * @param CreditCardRepository $creditCardRepository
     * @param BillingInfoRepository $billingInfoRepository
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(
        TransactionManager $transactionManager,
        PaymentRepository $paymentRepository,
        TransactionRepository $transactionRepository,
        CreditCardRepository $creditCardRepository,
        BillingInfoRepository $billingInfoRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->paymentRepository = $paymentRepository;
        $this->transactionRepository = $transactionRepository;
        $this->creditCardRepository = $creditCardRepository;
        $this->billingInfoRepository = $billingInfoRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function handler(PaymentFromBoForm $paymentFromBoForm): array
    {
        $paymentApiForms = $paymentFromBoForm->getPaymentApiForms();
        $creditCardForms = $paymentFromBoForm->getCreditCardForms();
        $billingInfoForms = $paymentFromBoForm->getBillingInfoForms();
        $orderId = $paymentFromBoForm->order->getId();
        $transactionProcessed = [];

        foreach ($paymentApiForms as $key => $paymentApiForm) {
            $creditCardId = null;
            /** @var CreditCardForm $creditCardForm */
            if (
                ($creditCardForm = ArrayHelper::getValue($creditCardForms, $key)) &&
                $creditCard = CreditCardApiService::getOrCreate($creditCardForm)
            ) {
                $this->creditCardRepository->save($creditCard);
                $creditCardId = $creditCard->cc_id;
            }

            $billingInfoId = null;
            /** @var BillingInfoForm $billingInfoForm */
            if (
                ($billingInfoForm = ArrayHelper::getValue($billingInfoForms, $key)) &&
                $billingInfo = BillingInfoApiService::getOrCreateBillingInfo($billingInfoForm, $orderId, $creditCardId)
            ) {
                $this->billingInfoRepository->save($billingInfo);
                $billingInfoId = $billingInfo->bi_id;
            }

            $invoiceId = null;
            if ($invoice = InvoiceApiService::createInvoice($paymentApiForm, $orderId, $billingInfoId)) {
                $this->invoiceRepository->save($invoice);
                $invoiceId = $invoice->inv_id;
            }

            $payment = PaymentApiService::getOrCreatePayment($paymentApiForm, $orderId, $invoiceId, $billingInfoId);
            $this->paymentRepository->save($payment);

            $payment = PaymentApiService::processingPayment($payment, $paymentApiForm);
            $this->paymentRepository->save($payment);

            if (TransactionApiService::existTransaction($paymentApiForm, $payment->pay_id)) {
                throw new \DomainException('Transaction already exist. Code:(' . $paymentApiForm->pay_auth_id . ')');
            }

            $transaction = TransactionApiService::createTransaction($paymentApiForm, $payment->pay_id, $invoiceId);
            $this->transactionRepository->save($transaction);
            $transactionProcessed[] = $transaction->tr_code;
        }
        return $transactionProcessed;
    }
}
