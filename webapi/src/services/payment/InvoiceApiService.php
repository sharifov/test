<?php

namespace webapi\src\services\payment;

use modules\flight\src\forms\api\PaymentApiForm;
use modules\invoice\src\entities\invoice\Invoice;
use yii\helpers\ArrayHelper;

/**
 * Class InvoiceApiService
 */
class InvoiceApiService
{
    public static function getOrCreateInvoice(
        PaymentApiForm $form,
        int $orderId,
        string $description = 'Created automatically from InvoiceApiService'
    ): ?Invoice {
        if ($invoice = self::getByOrderAndSum($orderId, (float)$form->pay_amount)) {
            return $invoice;
        }
        if (ArrayHelper::isIn($form->pay_type, [$form::TYPE_AUTHORIZE, $form::TYPE_CAPTURE])) {
            return Invoice::create(
                $orderId,
                (float) $form->pay_amount,
                $form->pay_currency,
                $description
            );
        }
        return null;
    }

    private static function getByOrderAndSum(int $orderId, float $sum): ?Invoice
    {
        return Invoice::findOne(['inv_order_id' => $orderId, 'inv_sum' => $sum]);
    }
}
