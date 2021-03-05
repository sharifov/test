<?php

namespace modules\order\src\events;

/**
 * Class OrderFileGeneratedEvent
 *
 * @property int $orderId
 * @property int $fileId
 * @property string $type
 */
class OrderFileGeneratedEvent
{
    public $orderId;
    public $fileId;
    public $type;

    public const TYPE_HOTEL_CONFIRMATION = 'hotelConfirmation';
    public const TYPE_ATTRACTION_CONFIRMATION = 'attractionConfirmation';
    public const TYPE_FLIGHT_CONFIRMATION = 'flightConfirmation';
    public const TYPE_RENT_CAR_CONFIRMATION = 'rentCarConfirmation';
    public const TYPE_ORDER_RECEIPT = 'orderReceipt';

    /**
     * @param int $orderId
     * @param int $fileId
     * @param string $type
     */
    public function __construct(int $orderId, int $fileId, string $type)
    {
        $this->orderId = $orderId;
        $this->fileId = $fileId;
        $this->type = $type;
    }
}
