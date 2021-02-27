<?php

namespace sales\helpers\text;

/**
 * Class CleanTextHelper
 */
class CleanTextHelper
{
    /**
     * @param string $text
     * @return string
     */
    public static function simpleText(string $text): string
    {
        $value = strip_tags($text);
        $value = preg_replace('/[^\w\s]/ui', '', $value);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return (string) $value;
    }

    public static function cleanText(string $text, string $allowedCharacters = '.,:-_()/'): string
    {
        $value = strip_tags($text);
        $value = preg_replace('/[^\w\s' . $allowedCharacters . ']/ui', '', $value);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return (string) $value;
    }

    public static function nameFileToTitle(string $text): string
    {
        $value = strip_tags($text);
        $value = preg_replace('/[^\w\s.]/ui', '', $value);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return (string) $value;
    }
}
