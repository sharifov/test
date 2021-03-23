<?php

namespace modules\flight\src\services\flightQuote;

use modules\flight\models\FlightQuote;
use modules\order\src\entities\order\Order;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteBookGuardService
 */
class FlightQuoteBookGuardService
{
    public static function guard(FlightQuote $flightQuote): bool
    {
        if ($flightQuote->isBooked()) {
            throw new \DomainException('Flight Quote already booked.');
        }
        if (!$flightQuote->isBookable()) {
            throw new \DomainException('Product Quote not in bookable status.');
        }
        if (!$order = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqOrder')) {
            throw new \DomainException('Not found Order');
        }
        /** @var Order $order */
        if (!$order->or_request_data) {
            throw new \DomainException('Request data not found in order');
        }
        if (!$hybridUid = ArrayHelper::getValue($order->or_request_data, 'Request.FlightRequest.uid')) {
            throw new \DomainException('HybridUid not found in order');
        }
        return true;
    }
}
