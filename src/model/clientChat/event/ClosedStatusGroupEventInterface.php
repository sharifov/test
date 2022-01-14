<?php

namespace src\model\clientChat\event;

interface ClosedStatusGroupEventInterface
{
    public function getChatId(): int;

    public function getOwnerId(): int;

    public function getShallowCase(): bool;
}
