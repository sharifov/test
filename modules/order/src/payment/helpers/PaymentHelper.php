<?php

namespace modules\order\src\payment\helpers;

use common\models\Payment;

/**
 * Class PaymentHelper
 */
class PaymentHelper
{
    public static function detectStatusFromSale(string $sourceStatus): ?int
    {
        if (stripos($sourceStatus, 'Capture') !== false) {
            return Payment::STATUS_CAPTURED;
        }
        return null;
    }
}
