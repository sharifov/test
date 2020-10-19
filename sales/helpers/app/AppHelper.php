<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 17:05
 */
namespace sales\helpers\app;

use Throwable;
use Yii;
use yii\helpers\VarDumper;

class AppHelper
{

    /**
     * @param \Throwable $throwable
     * @return string
     */
    public static function throwableFormatter(\Throwable $throwable): string
    {
        $str = 'Message: ' . $throwable->getMessage() . ' (code: '.$throwable->getCode().'), File: ' . $throwable->getFile() . ': line ' . $throwable->getLine();
        return $str;
    }

    /**
     * @param Throwable $throwable
     * @param bool $trace
     * @return array
     */
    public static function throwableLog(\Throwable $throwable, bool $trace = false): array
    {
        $data['message'] = $throwable->getMessage();
        $data['code'] = $throwable->getCode();
        $data['file'] = $throwable->getFile();
        $data['line'] = $throwable->getLine();

        if ($trace) {
            $data['trace'] = $throwable->getTrace();
        }

        return $data;
    }

    /**
     * @param Throwable $throwable
     * @param string $category
     * @param bool $formatted
     * @param int $typeCodeDelimiter
     */
    public static function throwableLogger(
        \Throwable $throwable,
        string $category,
        bool $formatted = true,
        int $typeCodeDelimiter = 0
    ): void {
        $errorMessage = $formatted ?
            self::throwableFormatter($throwable) :
            VarDumper::dumpAsString($throwable, 20);

        if ($throwable->getCode() < $typeCodeDelimiter) {
            Yii::info($errorMessage, 'info\\' . $category);
        } else {
            Yii::error($errorMessage, $category);
        }
    }

    /**
     * @param array $array
     * @param string $index
     * @param null $value
     * @return array
     */
    public static function filterByValue(array $array, string $index, $value = null): array
    {
        $newArray = [];
        if (is_array($array) && $array) {
            foreach (array_keys($array) as $key) {
                if ($value !== null) {
                    if ($array[$key][$index] === $value) {
                        $newArray[$key] = $array[$key];
                    }
                } else {
                    $newArray[$key] = $array[$key];
                }
            }
        }
        return $newArray;
    }

    /**
     * @param array $array
     * @param string $index
     * @param array $arrayVal
     * @return array
     */
    public static function filterByArray(array $array, string $index, array $arrayVal = []): array
    {
        $newArray = [];
        if (is_array($array) && $array) {
            foreach (array_keys($array) as $key) {
                if ($arrayVal) {
                    if (in_array($array[$key][$index], $arrayVal, true)) {
                        $newArray[$key] = $array[$key];
                    }
                } else {
                    $newArray[$key] = $array[$key];
                }
            }
        }
        return $newArray;
    }

    /**
     * @param array $array
     * @param string $index
     * @param float|null $valMin
     * @param float|null $valMax
     * @return array
     */
    public static function filterByRange(array $array, string $index, float $valMin = null, float $valMax = null): array
    {
        $newArray = [];
        if (is_array($array) && $array) {
            foreach (array_keys($array) as $key) {
                if ($valMin !== null && $valMax !== null) {
                    if ($array[$key][$index] >= $valMin && $array[$key][$index] <= $valMax) {
                        $newArray[$key] = $array[$key];
                    }
                } elseif ($valMin !== null && $valMax === null) {
                    if ($array[$key][$index] >= $valMin) {
                        $newArray[$key] = $array[$key];
                    }
                } elseif ($valMin === null && $valMax !== null) {
                    if ($array[$key][$index] <= $valMax) {
                        $newArray[$key] = $array[$key];
                    }
                } else {
                    $newArray[$key] = $array[$key];
                }
            }
        }
        return $newArray;
    }

    /**
     * @param array $array
     * @param string $index
     * @param string $value
     * @return array
     */
    public static function filterBySearchInValue(array $array, string $index, string $value): array
    {
        $newArray = [];
        if (is_array($array) && $array) {
            foreach (array_keys($array) as $key) {
                if (strpos($array[$key][$index], $value) !== false) {
                    $newArray[$key] = $array[$key];
                }
            }
        }
        return $newArray;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function deleteCacheByKey(string $key): bool
    {
        return Yii::$app->cache->delete($key);
    }


}
