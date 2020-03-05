<?php

namespace sales\helpers\user;

class UserDateTimeHelper
{
    /**
     * @param string $time ex. 2020-03-12:01:02
     * @param string|null $userTimeZone
     */
    public static function convertUserTimeToUtc(string $time, ?string $userTimeZone): \DateTimeImmutable
    {
        if (!$userTimeZone) {
            $userTimeZone = 'UTC';
        }
        return (new \DateTimeImmutable($time, new \DateTimeZone($userTimeZone)))->setTimezone(new \DateTimeZone('UTC'));
    }
}
