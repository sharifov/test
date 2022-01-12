<?php

namespace src\model\voip\phoneDevice\device;

class VoipDevice
{
    public static function getClientPrefix(): string
    {
        return 'client:';
    }

//    public static function getSipPrefix(): string
//    {
//        return 'sip:';
//    }

    /**
     * @param string $value
     * @return int
     * Valid only "client" prefix usage
     */
    public static function getUserId(string $value): int
    {
        return (int)preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * @param string|null $value
     * @return bool
     * Validation only "client" prefix usage
     */
    public static function isValid(?string $value): bool
    {
        if (!$value) {
            return false;
        }
        return strpos($value, self::getClientPrefix()) === 0;
    }
}
