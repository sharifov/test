<?php

namespace modules\product\src\entities\productQuoteChange\events;

interface ProductQuoteChangeInterface
{
    public function getId(): int;

    public function getClass(): string;
}
