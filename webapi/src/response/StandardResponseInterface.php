<?php

namespace webapi\src\response;

/**
 * Interface StandardResponseInterface
 *
 * @property string $messageDefault
 */
interface StandardResponseInterface
{
    public function getMessageDefault(): string;
    public function processStandardMessages(): array;
}
