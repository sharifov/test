<?php

namespace common\components\debug;

interface LoggerInterface
{
    public function timerStart(string $key);
    public function timerStop(string $key);
    public function log(Message $message);
    public function release(): void;
    public function messagesInlineMode(): void;
    public function messagesNewLineMode(): void;
    public function isOff(): bool;
}
