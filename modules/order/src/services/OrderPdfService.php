<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\Order;

/**
 * Class OrderPdfService
 */
class OrderPdfService
{
    /**
     * @param Order $order
     * @return bool
     */
    public static function processingFile(Order $order): bool
    {
        return true;
    }
}
