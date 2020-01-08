<?php

namespace webapi\src\response;

use webapi\src\response\messages\Message;

/**
 * Class SuccessResponse
 */
class SuccessResponse extends Response implements StandardResponseInterface
{
    use ProcessStandardMessageTrait;

    public const STATUS_CODE_DEFAULT = 200;
    public const MESSAGE_DEFAULT = 'OK';

    public function __construct(Message ...$messages)
    {
        $messages = $this->processStandardMessages(...$messages);
        parent::__construct(...$messages);
    }

    public function getResponse(): array
    {
        $this->sortUp('status', 'message');
        return $this->getResponseMessages();
    }

    public function getStatusCodeDefault(): int
    {
        return self::STATUS_CODE_DEFAULT;
    }

    public function getMessageDefault(): string
    {
        return self::MESSAGE_DEFAULT;
    }
}
