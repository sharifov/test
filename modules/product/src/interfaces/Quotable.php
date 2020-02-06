<?php

namespace modules\product\src\interfaces;

interface Quotable
{
    public static function findByProductQuote(int $productQuoteId): ?Quotable;
    public function serialize(): array;
    public function getId(): int;
}
