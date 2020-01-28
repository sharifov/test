<?php

namespace modules\invoice\src\exceptions;

use common\CodeExceptionsModule as Module;

class InvoiceCodeException
{
    public const INVOICE_NOT_FOUND = Module::INVOICE . 100;
    public const INVOICE_SAVE = Module::INVOICE . 101;
    public const INVOICE_REMOVE = Module::INVOICE . 102;

    public const INVOICE_STATUS_LOG_SAVE = Module::INVOICE . 110;
    public const INVOICE_STATUS_LOG_REMOVE = Module::INVOICE . 111;
}
