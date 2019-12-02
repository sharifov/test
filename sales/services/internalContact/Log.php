<?php

namespace sales\services\internalContact;

/**
 * Class Log
 *
 * @property array $messages
 */
class Log
{
    private $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * @param string $message
     */
    public function add(string $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @param string|null $prefix
     * @param string|null $category
     */
    public function release(?string $prefix, ?string $category): void
    {
        foreach ($this->messages as $message) {
            \Yii::error($prefix . $message, $category);
        }
        $this->messages = [];
    }
}
