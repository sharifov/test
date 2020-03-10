<?php

namespace frontend\widgets\notification;

use Webmozart\Assert\Assert;

/**
 * Class Notification
 *
 * @property string $id
 * @property string $command
 * @property string $message
 * @property string $type
 */
class Notification
{
    private const COMMAND_ADD = 'add';
    private const COMMAND_DELETE = 'delete';
    private const COMMAND_LIST = [
        self::COMMAND_ADD => self::COMMAND_ADD,
        self::COMMAND_DELETE => self::COMMAND_DELETE,
    ];

    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_NOTICE = 'notice';
    public const TYPE_ERROR = 'error';
    public const TYPE_DEFAULT = 'info';
    public const TYPE_LIST = [
        self::TYPE_INFO => self::TYPE_INFO,
        self::TYPE_SUCCESS => self::TYPE_SUCCESS,
        self::TYPE_NOTICE => self::TYPE_NOTICE,
        self::TYPE_ERROR => self::TYPE_ERROR,
    ];

    private $id;
    private $command;
    private $message;
    private $type;

    private function __construct(int $id, string $command, string $message, string $type)
    {
        Assert::oneOf($command, self::COMMAND_LIST);
        Assert::oneOf($type, self::TYPE_LIST);
        $this->id = $id;
        $this->command = $command;
        $this->message = $message;
        $this->type = $type;
    }

    public static function add(int $id, string $message): array
    {
        return (new self($id, self::COMMAND_ADD, $message, self::TYPE_INFO))->toArray();
    }

    public static function delete(int $id): array
    {
        return (new self($id, self::COMMAND_DELETE, '', self::TYPE_INFO))->toArray();
    }

    private function toArray(): array
    {
        return [
            'id' => $this->id,
            'command' => $this->command,
            'message' => $this->message,
            'type' => $this->type
        ];
    }
}
