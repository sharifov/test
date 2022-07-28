<?php

namespace modules\smartLeadDistribution\src\objects;

interface LeadRatingObjectInterface
{
    public static function getDTO(): string;

    public static function getAttributeList(): array;

    public static function getAttributes(): array;

    public static function getDataForField(string $attribute): array;
}
