<?php

namespace modules\order\src\flow\cancelOrder;

use modules\hotel\models\HotelQuote;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;

class FreeCancelChecker
{
    public string $message = '';

    public function can(Order $order): bool
    {
        if ($order->isCanceled() || $order->isCancelProcessing() || $order->isDeclined()) {
            $this->message = 'Unable to cancel because order in status ' . OrderStatus::getName($order->or_status_id);
            return false;
        }

        $hotelQuotes = array_filter($order->productQuotes, static function ($quote) {
            return $quote->isHotel() && $quote->isBooked();
        });

        foreach ($hotelQuotes as $hotelQuote) {
            /** @var HotelQuote $childQuote */
            $childQuote = $hotelQuote->childQuote;

            foreach ($childQuote->hotelQuoteRooms ?? [] as $hotelQuoteRoom) {
                if (!$hotelQuoteRoom->canFreeCancel()) {
                    $this->message = 'Unable to cancel order because Hotel room cannot be canceled free of charge';
                    return false;
                }
            }
        }

        return true;
    }
}
