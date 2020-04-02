<?php

namespace console\helpers;

use ReflectionClass;
use yii\helpers\Console;

/**
 * Class OutputHelper
 */
class OutputHelper
{
    /**
     * @param string $info
     * @param string $point (Class:method:point)
     * @param int $colorInfo
     * @param int $color
     */
    public function printInfo(string $info, string $point = '', $colorInfo = Console::FG_GREEN, $color = Console::FG_CYAN): void
    {
        printf(
            PHP_EOL . ' --- %s %s --- ' . PHP_EOL,
            $this->ansiFormat($info, $colorInfo),
            $this->ansiFormat('(' . $point . ')', $color)
        );
    }

    /**
     * @param int $statusCode
     * @return int
     */
    public static function getColorByStatusCode(int $statusCode): int
    {
         switch ($statusCode) {
            case 1: // success
                $colorInfo = Console::FG_GREEN;
                break;
            case -1: // fail
                $colorInfo = Console::FG_RED;
                break;
            case 0: // warning
                $colorInfo = Console::FG_YELLOW;
                break;
            default:
                $colorInfo = Console::FG_GREEN;
        }

        return $colorInfo;
    }

    /**
     * @param $className
     * @return string
     */
    public static function getShortClassName($className): string
    {
        try {
            $shortName = (new ReflectionClass($className))->getShortName();
        } catch (\Throwable $throwable) {
            $shortName = $className;
        }
        return $shortName;
    }

    /**
     * @param $string
     * @return string
     */
    private function ansiFormat($string): string
    {
        $args = func_get_args();
        array_shift($args);
        return Console::ansiFormat($string, $args);
    }
}