<?php

namespace common\components\debug;

/**
 * Class DummyLogger
 **/
class DummyLogger implements LoggerInterface
{
    public function timerStart(string $key): self
    {
        return $this;
    }

    public function timerStop(string $key): self
    {
        return $this;
    }

    public function log(Message $message): self
    {
        return $this;
    }

    public function release(): void
    {

    }

    public function messagesInlineMode(): void
    {

    }

    public function messagesNewLineMode(): void
    {

    }

    private function isMessagesInlineMode(): bool
    {
        return  true;
    }

    private function cleanMessages(): void
    {

    }

    public function isOff(): bool
    {
        return  true;
    }
}
