<?php

namespace common\components\debug;

interface Target
{
    public function log(Message $message): void;
    public function logs(Message ...$messages): void;
    public function isDeffer(): bool;
}
