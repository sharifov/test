<?php

namespace common\components\debug;

class Message
{
    private const LEVEL_INFO = 'info';
    private const LEVEL_WARNING = 'warning';
    private const LEVEL_ERROR = 'error';
    private const LEVEL_SUCCESS = 'success';

    private $message;
    private $level;
    private $newLine = true;

    private function __construct(string $message, string $level)
    {
        if (!in_array($level, [self::LEVEL_INFO, self::LEVEL_WARNING, self::LEVEL_ERROR, self::LEVEL_SUCCESS], true)) {
            throw new \InvalidArgumentException('level is incorrect');
        }
        $this->message = $message;
        $this->level = $level;
    }

    public static function info(string $message): self
    {
        return new self($message, self::LEVEL_INFO);
    }

    public static function warning(string $message): self
    {
        return new self($message, self::LEVEL_WARNING);
    }

    public static function error(string $message): self
    {
        return new self($message, self::LEVEL_ERROR);
    }

    public static function success(string $message): self
    {
        return new self($message, self::LEVEL_SUCCESS);
    }

    public static function start(string $message): self
    {
        $msg = self::warning($message);
        $msg->continueLine();
        return $msg;
    }

    public static function finish(string $message): self
    {
        return self::warning($message);
    }

    public function continueLine(): void
    {
        $this->newLine = false;
    }

    public function fromNewLine(): bool
    {
        return $this->newLine === true;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isInfo(): bool
    {
        return $this->level === self::LEVEL_INFO;
    }

    public function isWarning(): bool
    {
        return $this->level === self::LEVEL_WARNING;
    }

    public function isError(): bool
    {
        return $this->level === self::LEVEL_ERROR;
    }

    public function isSuccess(): bool
    {
        return $this->level === self::LEVEL_SUCCESS;
    }

    public function add(string $message): void
    {
        $this->message .= $message;
    }
}
