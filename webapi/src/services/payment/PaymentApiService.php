<?php

namespace webapi\src\services\payment;

use common\models\Payment;
use common\models\PaymentMethod;
use modules\flight\src\forms\api\PaymentApiForm;

/**
 * Class PaymentApiService
 */
class PaymentApiService
{
    /**
     * @param PaymentApiForm $form
     * @param int $orderId
     * @param int|null $invoiceId
     * @param int|null $billingInfoId
     * @return Payment
     */
    public static function getOrCreatePayment(PaymentApiForm $form, int $orderId, ?int $invoiceId, ?int $billingInfoId): Payment
    {
        if ($payment = Payment::findLastByCodeAndOrder($form->pay_code, $orderId)) {
            if ($invoiceId && !$payment->pay_invoice_id) {
                $payment->pay_invoice_id = $invoiceId;
            }
            return $payment;
        }
        $payment = Payment::create(
            self::getPaymentMethodId($form->pay_method_key),
            $form->pay_date,
            $form->pay_amount,
            $form->pay_currency,
            $invoiceId,
            $orderId,
            $form->pay_code,
            $form->pay_description,
            $billingInfoId
        );
        $payment->inProgress();
        return $payment;
    }

    public static function processingPayment(Payment $payment, PaymentApiForm $form): Payment
    {
        if ($form->pay_type === PaymentApiForm::TYPE_AUTHORIZE && $payment->isAuthorizable()) {
            $payment->authorized();
            $payment->changeAmount($form->pay_amount);
        } elseif ($form->pay_type === PaymentApiForm::TYPE_CAPTURE && $payment->isCompletable()) {
            $payment->completed();
            $payment->changeAmount($form->pay_amount);
        } elseif ($form->pay_type === PaymentApiForm::TYPE_REFUND && $payment->isRefundable()) {
            $payment->refund();
            $amount = $payment->pay_amount - $form->pay_amount;
            if ($amount < 0) {
                \Yii::error(
                    [
                        'message' => 'Refund - payment is less than zero',
                        'amount' => $amount,
                        'payment' => $payment->toArray(),
                        'paymentApiForm' => $form->toArray()
                    ],
                    'PaymentApiService:processingPayment:Refund:PaymentLessThanZero'
                );
                $amount = 0.00;
            }
            $payment->changeAmount($amount);
        }
        return $payment;
    }

    private static function getPaymentMethodId(string $key): ?int
    {
        if ($paymentMethod = PaymentMethod::findOne(['pm_key' => $key])) {
            return $paymentMethod->pm_id;
        }
        return null;
    }
}
