<?php

namespace modules\user\src\update;

use Throwable;

/**
 * Class UpdateUserException
 *
 * @property array $errors
 * @property int $targetUserId
 * @property int $updaterUserId
 */
class UpdateUserException extends \DomainException
{
    public array $errors;
    public int $targetUserId;
    public int $updaterUserId;

    public function __construct(array $errors, int $targetUserId, int $updaterUserId, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errors = $errors;
        $this->targetUserId = $targetUserId;
        $this->updaterUserId = $updaterUserId;
        parent::__construct($message, $code, $previous);
    }
}
