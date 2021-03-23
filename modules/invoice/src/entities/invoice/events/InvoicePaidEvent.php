<?php

namespace modules\invoice\src\entities\invoice\events;

/**
 * Class InvoicePaidEvent
 *
 * @property int $invoiceId
 * @property $orderId
 */
class InvoicePaidEvent implements Invoiceable
{
    public int $invoiceId;
    public $orderId;

    public function __construct(int $invoiceId, ?int $orderId)
    {
        $this->invoiceId = $invoiceId;
        $this->orderId = $orderId;
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }
}
