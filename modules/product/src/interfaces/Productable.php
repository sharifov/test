<?php

namespace modules\product\src\interfaces;

interface Productable
{
    public static function create(int $productId);
    public function releaseEvents(): array;
}
