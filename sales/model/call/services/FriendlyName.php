<?php

namespace sales\model\call\services;

use thamtech\uuid\helpers\UuidHelper;

class FriendlyName
{
    public static function next(): string
    {
        return str_replace('-', '', UuidHelper::uuid());
    }

    public static function nextWithSid(string $sid): string
    {
        return substr($sid, 0, 16) . substr(str_replace('-', '', UuidHelper::uuid()), 0, 16);
    }
}
