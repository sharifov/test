<?php

namespace sales\helpers\text;

/**
 * Class SimpleTextHelper
 */
class SimpleTextHelper
{
    /**
     * @param string $text
     * @return string
     */
    public static function clean(string $text): string
    {
        $value = preg_replace('/[^\w\s]/ui', '', $text);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return (string) $value;
    }
}
