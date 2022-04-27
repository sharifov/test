<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 17:05
 */

namespace src\helpers\app;

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
        $str = 'Message: ' . $throwable->getMessage() . ' (code: ' . $throwable->getCode() . '), File: ' . $throwable->getFile() . ': line ' . $throwable->getLine();
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
            $data['trace'] = $throwable->getTraceAsString();
        }

        return $data;
    }

    public static function mergeThrowableWithData(\Throwable $t, array $data, bool $trace = false): array
    {
        return array_merge(self::throwableLog($t, $trace), $data);
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
            self::throwableLog($throwable) :
            self::throwableFormatter($throwable);

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
     * @param array $indexPath
     * @param null $value
     * @return array
     */
    public static function filterByValueMultiLevel(array $array, array $indexPath, $value = null): array
    {
        $newArray = [];

        if (is_array($array) && $array) {
            foreach (array_keys($array) as $key) {
                if ($value !== null) {
                    $current = $array[$key];
                    foreach ($indexPath as $step) {
                        $current = $current[$step];
                        if ($current === $value) {
                            $newArray[$key] = $array[$key];
                        }
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
     * @param string $index (array key)
     * @param array $arrayVal (array of filter values)
     * @return array
    */
    public static function filterByArrayContainValues(array $array, string $index, array $arrayVal = []): array
    {
        $newArray = [];
        if ($array) {
            foreach (array_keys($array) as $key) {
                if ($arrayVal) {
                    $match = false;
                    foreach ($arrayVal as $needle) {
                        if (stripos($array[$key][$index], $needle) !== false) {
                            $match = true;
                        }
                        if ($match) {
                            $newArray[$key] = $array[$key];
                        }
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
     * @param array $data
     * @param int $level
     * @param int $currentLevel
     * @return array
     */
    public static function shotArrayData(array $data, int $level = 3, int $currentLevel = 0): array
    {
        $dataResponse = [];
        $currentLevel++;
        if ($data) {
            foreach ($data as $key => $itemData) {
                if (!empty($itemData) && is_array($itemData)) {
                    if ($currentLevel >= $level) {
                        $dataResponse[$key] = json_encode($itemData);
                    } else {
                        $dataResponse[$key] = self::shotArrayData($itemData, $level, $currentLevel);
                    }
                } else {
                    $dataResponse[$key] = $itemData;
                }
            }
            unset($data);
        }
        return $dataResponse;
    }


    /**
     * Random selection based on the weight of each item.
     * @param array $data Array to search for random element
     * @param string $column Array parameter containing the "weight" of the probability
     * @return int Index of found element in $data array
     */
    public static function getRandomProbabilityIndex(array $data, string $column = 'ver'): int
    {
        try {
            $rand = mt_rand(1, array_sum(array_column($data, $column)));
            $cur = $prev = 0;
            for ($i = 0, $count = count($data); $i < $count; ++$i) {
                $prev += $i != 0 ? $data[$i - 1][$column] : 0;
                $cur += $data[$i][$column];
                if ($rand > $prev && $rand <= $cur) {
                    return $i;
                }
            }
        } catch (\Throwable $throwable) {
            return -1;
        }

        return -1;
    }

    /**
     * Check incoming string on valid date
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
}
