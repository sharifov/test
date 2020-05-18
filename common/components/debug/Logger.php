<?php

namespace common\components\debug;

/**
 * Class Logger
 *
 * @property bool $enabled
 * @property Message[] $queue
 * @property bool $defer
 * @property Target $target
 * @property bool $messagesInline
 * @property Timer $timer
 */
class Logger implements LoggerInterface
{
    private $enabled;
    private $queue = [];
    private $defer;
    private $target;
    private $messagesInline = false;
    private $timer;
    private $timerStopMessage;

    public function __construct(bool $enabled, Target $target)
    {
        $this->enabled = $enabled;
        $this->defer = $target->isDeffer();
        $this->target = $target;
        $this->timer = new Timer();
    }

    public function timerStart(string $key): self
    {
        if ($this->isOff()) {
            return $this;
        }

        $this->timer->start($key);
        return $this;
    }

    public function timerStop(string $key): self
    {
        if ($this->isOff()) {
            return $this;
        }

        $this->timerStopMessage = ' | Execute time: ' . $this->timer->stop($key);
        return $this;
    }

    public function log(Message $message): self
    {
        if ($this->isOff()) {
            return $this;
        }

        if ($this->timerStopMessage) {
            $message->add($this->timerStopMessage);
            $this->timerStopMessage = null;
        }

        if ($this->isMessagesInlineMode()) {
            $message->continueLine();
        }

        if ($this->defer === true) {
            $this->queue[] = $message;
            return $this;
        }

        $this->target->log($message);

        return $this;
    }

    public function release(): void
    {
        if ($this->queue) {
            $this->log(Message::info(''));
        }
        $messages = $this->queue;
        $this->cleanMessages();
        if ($this->isOff()) {
            return;
        }
        $this->target->logs(...$messages);
    }

    public function messagesInlineMode(): void
    {
        $this->log(Message::info(''));
        $this->messagesInline = true;
    }

    public function messagesNewLineMode(): void
    {
        $this->messagesInline = false;
    }

    private function isMessagesInlineMode(): bool
    {
        return $this->messagesInline === true;
    }

    private function cleanMessages(): void
    {
        $this->queue = [];
    }

    public function isOff(): bool
    {
        return $this->enabled === false;
    }
}
