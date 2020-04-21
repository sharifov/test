<?php

namespace common\components\debug;

use Webmozart\Assert\Assert;
use Yii;

/**
 * Class DbTarget
 *
 * @property string $category
 * @property string $level
 */
class DbTarget implements Target
{
    public const LEVEL_INFO = \yii\log\Logger::LEVEL_INFO;
    public const LEVEL_WARNING = \yii\log\Logger::LEVEL_WARNING;
    public const LEVEL_ERROR = \yii\log\Logger::LEVEL_ERROR;

    private $category;
    private $level;

    public function __construct(string $category, int $level = self::LEVEL_INFO)
    {
        Assert::oneOf($level, [self::LEVEL_INFO, self::LEVEL_WARNING, self::LEVEL_ERROR]);
        $this->category = $category;
        $this->level = $level;
    }

    public function log(Message $message): void
    {
        Yii::getLogger()->log($this->process($message), $this->level, $this->category);
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
        Yii::getLogger()->log($out, $this->level, $this->category);
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
