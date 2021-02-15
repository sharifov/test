<?php

namespace modules\flight\src\dto\ngs;

class QuoteNgsDataDto
{
    public string $name = '';

    public int $stars = 0;

    public array $list = [];

    public function __construct(array $flightQuoteNgsData = [])
    {
        $this->name = $flightQuoteNgsData['name'] ?? '';
        $this->stars = $flightQuoteNgsData['stars'] ?? '';
        $this->list = $flightQuoteNgsData['list'] ?? [];
    }
}
