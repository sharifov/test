<?php

namespace sales\services\phone\callFilterGuard;

/**
 * Interface CheckServiceInterface
 */
interface CheckServiceInterface
{
    public function getTrustPercent(): int;
    public function getPhone(): string;
    public function default(): CheckServiceInterface;
    public function getResponseData(): array;
}
