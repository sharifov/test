<?php

namespace webapi\src\response;

use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;

/**
 * Class ErrorResponse
 */
class ErrorResponse extends Response implements StandardResponseInterface
{
    use ProcessStandardMessageTrait;

    public const STATUS_CODE_DEFAULT = 422;
    public const MESSAGE_DEFAULT = 'Error';
    public const CODE_DEFAULT = 0;

    public function __construct(Message ...$messages)
    {
        $messages = $this->processStandardMessages(...$messages);
        $messages = $this->processMessages(...$messages);
        parent::__construct(...$messages);
    }

    public function getResponse(): array
    {
        $this->sortUp('status', 'message');
        return $this->getResponseMessages();
    }

    public function getMessageDefault(): string
    {
        return self::MESSAGE_DEFAULT;
    }

    public function getStatusCodeDefault(): int
    {
        return self::STATUS_CODE_DEFAULT;
    }

    private function processMessages(Message ...$messages)
    {
        $errorsExist = false;
        $codeExist = false;

        foreach ($messages as $message) {

            if ($message->isErrors()) {
                $errorsExist = true;
            }

            if ($message->isCode()) {
                $codeExist = true;
            }
        }

        if (!$errorsExist) {
            $messages[] = new ErrorsMessage([]);
        }

        if (!$codeExist) {
            $messages[] = new CodeMessage(self::CODE_DEFAULT);
        }

        return $messages;
    }
}
