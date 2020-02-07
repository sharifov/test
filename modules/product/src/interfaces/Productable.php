<?php

namespace modules\product\src\interfaces;

interface Productable
{
    public static function create(int $productId);
    public function releaseEvents(): array;
    public function serialize(): array;
    public function getId(): int;
    public static function findByProduct(int $productId): ?Productable;
}
