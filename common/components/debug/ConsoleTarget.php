<?php

namespace common\components\debug;

use yii\helpers\Console;

class ConsoleTarget implements Target
{
    public function log(Message $message): void
    {
        if ($message->fromNewLine()) {
            echo PHP_EOL;
        }
        echo $this->process($message);
    }

    public function logs(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->log($message);
        }
        echo PHP_EOL;
    }

    public function isDeffer(): bool
    {
        return false;
    }

    private function process(Message $message): string
    {
        if ($message->isInfo()) {
            return Console::renderColoredString('%w' . $message->getMessage() . '%n');
        }
        if ($message->isError()) {
            return Console::renderColoredString('%r' . $message->getMessage() . '%n');
        }
        if ($message->isWarning()) {
            return Console::renderColoredString('%y' . $message->getMessage() . '%n');
        }
        if ($message->isSuccess()) {
            return Console::renderColoredString('%g' . $message->getMessage() . '%n');
        }
        return $message->getMessage();
    }
}
