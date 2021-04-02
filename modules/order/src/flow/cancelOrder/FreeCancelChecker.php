<?php

namespace modules\order\src\flow\cancelOrder;

use modules\hotel\models\HotelQuote;
use modules\order\src\entities\order\Order;

class FreeCancelChecker
{
    public function can(Order $order): bool
    {
        if ($order->isCanceled() || $order->isCancelProcessing() || $order->isDeclined()) {
            return false;
        }

        $hotelQuotes = array_filter($order->productQuotes, static function ($quote) {
            return $quote->isHotel() && $quote->isBooked();
        });

        foreach ($hotelQuotes as $hotelQuote) {
            /** @var HotelQuote $childQuote */
            $childQuote = $hotelQuote->childQuote;
            if (!$childQuote->canFreeCancel()) {
                return false;
            }
        }

        return true;
    }
}
