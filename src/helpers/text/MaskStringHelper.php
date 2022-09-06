<?php

namespace src\helpers\text;

class MaskStringHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function masking(string $string): string
    {
        $length = strlen($string);
        $visibleCount = (int)round($length / 4);
        $hiddenCount = $length - ($visibleCount * 1);
        return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
    }

    /**
     * @param array $data
     * @param bool $maskKey
     * @return array
     */
    public static function maskArray(array $data, bool $maskKey = false): array
    {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $index => $value) {
                if (!is_array($value)) {
                    if ($maskKey) {
                        $result[self::masking($index)] = self::masking($value);
                    } else {
                        $result[$index] = self::masking($value);
                    }
                } else {
                    if ($maskKey) {
                        $result[self::masking($index)] = self::maskArray($value, $maskKey);
                    } else {
                        $result[$index] = self::maskArray($value);
                    }
                }
            }
        }
        return $result;
    }
}
