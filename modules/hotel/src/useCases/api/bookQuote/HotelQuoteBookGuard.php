<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\models\HotelQuote;
use modules\order\src\entities\order\Order;
use yii\helpers\ArrayHelper;

use function Amp\Promise\timeoutWithDefault;

/**
 * Class HotelQuoteBookGuard
 * @package modules\hotel\src\useCases\api\bookQuote
 */
class HotelQuoteBookGuard
{
    /**
     * @param HotelQuote $model
     * @return HotelQuote
     */
    public static function guard(HotelQuote $model): HotelQuote
    {
        if ($model->isBooked()) {
            throw new \DomainException('Hotel Quote already booked. (BookingId:' . $model->hq_booking_id . ')');
        }
        if (!$model->isBookable()) {
            throw new \DomainException('Product Quote not in allowed status');
        }
        if (!$order = ArrayHelper::getValue($model, 'hqProductQuote.pqOrder')) {
            throw new \DomainException('Not found Order');
        }
        return $model;
    }
}
