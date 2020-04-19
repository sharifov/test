<?php

namespace common\components\debug;

/**
 * Class DbTarget
 *
 * @property string $category
 */
class DbTarget implements Target
{
    private $category;

    public function __construct(string $category)
    {
        $this->category = $category;
    }

    public function log(Message $message): void
    {
        \Yii::info($this->process($message), $this->category);
    }

    public function logs(Message ...$messages): void
    {
        $out = '';
        $count = count($messages);
        foreach ($messages as $key => $message) {
            if (($key + 1) === $count && $message->getMessage() === '') {
                break;
            }
            if ($message->fromNewLine()) {
                $out .= PHP_EOL;
            }
            $out .= $this->process($message);

        }
        \Yii::info($out, $this->category);
    }

    public function isDeffer(): bool
    {
        return true;
    }

    private function process(Message $message): string
    {
        return $message->getMessage();
    }
}
