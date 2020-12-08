<?php

namespace console\socket\Commands;

use Throwable;

class ErrorsException extends \DomainException
{
    private array $errors;

    public function __construct(array $errors, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
