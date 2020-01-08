<?php

namespace webapi\src\response;

use webapi\src\response\messages\Message;
use yii\base\Arrayable;
use yii\base\BaseObject;

/**
 * Class Response
 *
 * @property int $responseStatusCode
 * @property array $response
 * @property int $statusCodeDefault
 * @property array $responseMessages
 *
 * @property Message[] $messages
 */
abstract class Response extends BaseObject implements Arrayable
{
    private $statusCode;

    protected $messages = [];

    public function __construct(Message ...$messages)
    {
        $this->addMessages(...$messages);
        parent::__construct([]);
    }

    public function addMessage(Message $message): void
    {
        $key = $message->getKey();
        $this->messages[$key] = new Message($key, $message->getValue());
    }

    public function addMessages(Message ...$messages): void
    {
        foreach ($messages as $value) {
            $this->addMessage($value);
        }
    }

    public function removeMessage(Message $message): void
    {
        $this->removeMessageByKey($message->getKey());
    }

    public function removeMessages(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->removeMessage($message);
        }
    }

    public function removeMessageByKey($key): void
    {
        if (isset($this->messages[$key])) {
            unset($this->messages[$key]);
        }
    }

    public function removeMessagesByKeys(array $keys): void
    {
        foreach ($keys as $key) {
            $this->removeMessageByKey($key);
        }
    }

    public function getMessageByKey($key): ?Message
    {
        return $this->messages[$key] ?? null;
    }

    public function setStatusCode(int $value): void
    {
        $this->statusCode = $value;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    protected function getResponseMessages(): array
    {
        $response = [];
        foreach ($this->messages as $message) {
            if (!$message->isStatusCode()) {
                $response[$message->getKey()] = $message->getValue();
            }
        }
        return $response;
    }

    public function sortUp(string ...$keys)
    {
        $this->sort(true, ...$keys);
    }

    public function sortDown(string ...$keys)
    {
        $this->sort(false, ...$keys);
    }

    public function sort(bool $isUp, string ...$keys)
    {
        $sortValues = [];
        foreach ($keys as $key) {
            if (isset($this->messages[$key])) {
                $sortValues[$key] = $this->messages[$key];
                unset($this->messages[$key]);
            }
        }

        $oldMessages = $this->messages;
        $this->truncateMessages();

        if ($isUp) {
            foreach ($sortValues as $key => $sortValue) {
                $this->messages[$key] = $sortValue;
            }

            foreach ($oldMessages as $key => $oldMessage) {
                $this->messages[$key] = $oldMessage;
            }
        } else {
            foreach ($oldMessages as $key => $oldMessage) {
                $this->messages[$key] = $oldMessage;
            }

            foreach ($sortValues as $key => $sortValue) {
                $this->messages[$key] = $sortValue;
            }
        }
    }

    public function truncateMessages()
    {
        $this->messages = [];
    }

    protected function getMessageValueByKey($key)
    {
        if (!isset($this->messages[$key])) {
            return null;
        }
        return $this->messages[$key]->getValue();
    }

    public function getResponseStatusCode(): int
    {
        if (isset($this->messages[Message::STATUS_CODE_MESSAGE])) {
            $statusCode = $this->messages[Message::STATUS_CODE_MESSAGE]->getValue();
        } else {
            $statusCode = $this->statusCode;
        }
        return (int)($statusCode ?: $this->getStatusCodeDefault());
    }

    public function fields(): array
    {
        return [];
    }

    public function extraFields(): array
    {
        return [];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return $this->getResponse();
    }

    abstract public function getResponse(): array;

    abstract public function getStatusCodeDefault(): int;
}
