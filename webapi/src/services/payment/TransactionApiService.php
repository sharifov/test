<?php

namespace webapi\src\services\payment;

use common\models\Transaction;
use modules\flight\src\forms\api\PaymentApiForm;

/**
 * Class TransactionApiService
 */
class TransactionApiService
{
    public static function existTransaction(PaymentApiForm $form, int $paymentId): bool
    {
        return Transaction::find()
            ->where(['tr_payment_id' => $paymentId])
            ->andWhere(['tr_code' => (string) $form->pay_auth_id])
            ->exists();
    }

    public static function createTransaction(PaymentApiForm $form, int $paymentId): Transaction
    {
        return Transaction::create(
            $form->pay_amount,
            (string) $form->pay_auth_id,
            $form->pay_date,
            $paymentId,
            self::getTypeId($form->pay_type),
            $form->pay_currency,
            $form->pay_description
        );
    }

    private static function getTypeId(string $typeKey)
    {
        if ($typeKey === PaymentApiForm::TYPE_AUTHORIZE) {
            return Transaction::TYPE_AUTHORIZATION;
        }
        if ($typeKey === PaymentApiForm::TYPE_CAPTURE) {
            return Transaction::TYPE_CAPTURE;
        }
        if ($typeKey === PaymentApiForm::TYPE_REFUND) {
            return Transaction::TYPE_REFUND;
        }
        if ($typeKey === PaymentApiForm::TYPE_VOID) {
            return Transaction::TYPE_VOID;
        }
        throw new \DomainException('Unknown transaction type (' . $typeKey . ')');
    }
}
