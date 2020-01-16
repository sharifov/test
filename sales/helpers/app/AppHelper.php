<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 17:05
 */
namespace sales\helpers\app;


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
}