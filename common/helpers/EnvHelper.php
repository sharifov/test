<?php

/**
 * @author AlexConnor
 * @cdata 2021-06-29
 */

//namespace common\helpers;

if (! function_exists('env')) {

    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param string|null $type
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, ?string $type = null, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'null':
            case '(null)':
                return null;

            case 'empty':
            case '(empty)':
                return '';
        }

        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return substr($value, 1, -1);
        }

        if ($type) {
            switch ($type) {
                case 'integer':
                case 'int':
                    return (int)$value;
                case 'string':
                case 'str':
                    return (string)$value;
                case 'null':
                    return null;
                case 'boolean':
                case 'bool':
                    return (bool)$value;
                case 'float':
                    return (float)$value;
                case 'array':
                    return (array)$value;
            }
        }

        return $value;
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (! function_exists('str_ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_ends_with(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('str_starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_starts_with(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array $array
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function array_get(array $array, ?string $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
