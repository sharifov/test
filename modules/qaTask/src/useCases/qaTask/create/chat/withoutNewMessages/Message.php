<?php

namespace modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages;

use Webmozart\Assert\Assert;

/**
 * Class Message
 *
 * @property int $type
 * @property int $chatId
 * @property int|null $taskId
 */
class Message
{
    private const VALID = 1;
    private const INVALID = 0;

    private $type;
    private $chatId;
    private $taskId;

    public static function createValid(int $chatId, int $taskId): self
    {
        return new self(self::VALID, $chatId, $taskId);
    }

    public static function createInvalid(int $chatId): self
    {
        return new self(self::INVALID, $chatId, null);
    }

    private function __construct(int $type, int $chatId, ?int $taskId)
    {
        Assert::oneOf($type, [self::VALID, self::INVALID]);
        $this->type = $type;
        $this->chatId = $chatId;
        $this->taskId = $taskId;
    }

    public function isValid(): bool
    {
        return $this->type === self::VALID;
    }

    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }
}
