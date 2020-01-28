<?php

namespace modules\invoice\src\entities\invoice;

use modules\invoice\src\exceptions\InvoiceCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class InvoiceRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class InvoiceRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Invoice
    {
        if ($invoice = Invoice::findOne($id)) {
            return $invoice;
        }
        throw new NotFoundException('Invoice is not found', InvoiceCodeException::INVOICE_NOT_FOUND);
    }

    public function save(Invoice $invoice): int
    {
        if (!$invoice->save(false)) {
            throw new \RuntimeException('Saving error', InvoiceCodeException::INVOICE_SAVE);
        }
        $this->eventDispatcher->dispatchAll($invoice->releaseEvents());
        return $invoice->inv_id;
    }

    public function remove(Invoice $invoice): void
    {
        if (!$invoice->delete()) {
            throw new \RuntimeException('Removing error', InvoiceCodeException::INVOICE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($invoice->releaseEvents());
    }
}
