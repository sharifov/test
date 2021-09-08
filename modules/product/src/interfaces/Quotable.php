<?php

namespace modules\product\src\interfaces;

interface Quotable
{
    public static function findByProductQuote(int $productQuoteId): ?Quotable;
    public function serialize(): array;
    public function getId(): int;
    public function getProcessingFee(): float;
    public function getSystemMarkUp(): float;
    public function getAgentMarkUp(): float;
    public function getQuoteDetailsPageUrl(): string;
    public function getDiffUrlOriginReprotectionQuotes(): string;
}
