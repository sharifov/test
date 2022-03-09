<?php

namespace src\helpers\text;

/**
 * Class HashHelper
 */
class HashHelper
{
    public static function generateHashFromArray(array $data): string
    {
        ksort($data, SORT_STRING);
        return md5(serialize($data));
    }
}
