<?php

namespace modules\invoice\src\entities\invoice\events;

interface Invoiceable
{
    public function getInvoiceId(): int;
}
