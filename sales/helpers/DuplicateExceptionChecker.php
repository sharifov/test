<?php

namespace sales\helpers;

class DuplicateExceptionChecker
{
    public static function isDuplicate(string $message): bool
    {
        return strpos($message, 'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry') === 0;
    }
}
