<?php

namespace frontend\helpers;

/**
 * Class SlugHelper
 */
class SlugHelper
{
    /**
     * @param string $key
     * @return string|null
     */
    public static function prepare(string $key): ?string
    {
        $key = strtolower(trim($key));
        return preg_replace('/[^a-z0-9_]+/', '_', $key);
    }
}
