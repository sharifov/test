<?php

namespace sales\helpers\text;

/**
 * Class SecureStringHelper
 */
class SecureStringHelper
{
    /**
     * @param string $string
     * @param int $limit
     * @param string $delimiter
     * @return string
     */
    public static function generate(string $string, int $limit = 2, string $delimiter = '...'): string
    {
        if (strlen($string) <= ($limit * 2) + 1) {
            return $delimiter;
        }
        return substr($string, 0, $limit) . $delimiter . substr($string, -$limit);
    }
}
