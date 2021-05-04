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
    public static function createInvoice(
        PaymentApiForm $form,
        int $orderId,
        ?int $billingInfoId,
        string $description = 'Created automatically from InvoiceApiService'
    ): ?Invoice {
        if (ArrayHelper::isIn($form->pay_type, [$form::TYPE_CAPTURE])) {
            return Invoice::create(
                $orderId,
                (float) $form->pay_amount,
                $form->pay_currency,
                $description,
                $billingInfoId
            );
        }
        return null;
    }
}
