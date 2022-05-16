<?php

declare(strict_types=1);

namespace src\helpers\product;

use Exception;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use src\helpers\app\AppHelper;
use DateTime;
use Yii;

class ProductQuoteRefundHelper
{
    /**
     * @param ProductQuoteRefund $productQuote
     * @return bool
     */
    public static function checkingExpirationDate(ProductQuoteRefund $productQuote): bool
    {
        try {
            return !$productQuote->pqr_expiration_dt || (new DateTime($productQuote->pqr_expiration_dt)) >= (new DateTime());
        } catch (Exception $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'ProductQuoteRefundHelper:checkingExpirationDate:failed');
            return false;
        }
    }
}
