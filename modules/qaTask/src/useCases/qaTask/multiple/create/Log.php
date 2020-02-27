<?php

namespace modules\qaTask\src\useCases\qaTask\multiple\create;

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

    public function count(): int
    {
        return count($this->messages);
    }

    public function format(): string
    {
        if (empty($this->messages)) {
            return '';
        }

        $out = '<ul>';
        foreach ($this->messages as $message) {
            $out .= '<li>' . $message->format() . '</li>';
        }
        return $out . '</ul>';
    }
}
