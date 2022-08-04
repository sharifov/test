<?php

namespace modules\product\src\entities\productQuoteChange\events;

interface ProductQuoteChangeInterface
{
    public function getProductQuoteChangeId(): int;

    public function getClass(): string;
}
