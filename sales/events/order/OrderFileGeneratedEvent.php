<?php

namespace sales\events\quote;

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

    /**
     * @param int $orderId
     * @param int $fileId
     * @param string $type // hotelConfirmation|flightTicket|orderReceipt
     */
    public function __construct(int $orderId, int $fileId, string $type)
    {
        $this->orderId = $orderId;
        $this->fileId = $fileId;
        $this->type = $type;
    }
}
