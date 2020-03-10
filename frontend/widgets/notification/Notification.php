<?php

namespace frontend\widgets\notification;

use Webmozart\Assert\Assert;

/**
 * Class Notification
 *
 * @property string $id
 * @property string $command
 * @property string $message
 */
class Notification
{
    private const COMMAND_ADD = 'add';
    private const COMMAND_DELETE = 'delete';
    private const COMMAND_LIST = [
        self::COMMAND_ADD => self::COMMAND_ADD,
        self::COMMAND_DELETE => self::COMMAND_DELETE,
    ];

    private $id;
    private $command;
    private $message;

    private function __construct(int $id, string $command, string $message)
    {
        Assert::oneOf($command, self::COMMAND_LIST);
        $this->id = $id;
        $this->command = $command;
        $this->message = $message;
    }

    public static function add(int $id, string $message): array
    {
        return (new self($id, self::COMMAND_ADD, $message))->toArray();
    }

    public static function delete(int $id): array
    {
        return (new self($id, self::COMMAND_DELETE, ''))->toArray();
    }

    private function toArray(): array
    {
        return [
            'id' => $this->id,
            'command' => $this->command,
            'message' => $this->message,
        ];
    }
}
