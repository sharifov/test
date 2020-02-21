<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\processingQuality;

/**
 * Class Log
 *
 * @property Message[] $messages
 */
class Log
{
    private $messages = [];

    public function add(Message $message): void
    {
        $this->messages[] = $message;
    }

    public function getCount(): int
    {
        return count($this->messages);
    }

    public function getValidCount(): int
    {
        $count = 0;
        foreach ($this->messages as $message) {
            if ($message->isValid()) {
                $count++;
            }
        }
        return $count;
    }

    public function getInvalidCount(): int
    {
        $count = 0;
        foreach ($this->messages as $message) {
            if (!$message->isValid()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
