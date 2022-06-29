<?php

namespace src\exceptions;

class ModelNotFoundException extends \RuntimeException
{
    public function __construct(string $className = '')
    {
        $message = sprintf('Record of model %s not found.', $className);
        parent::__construct($message);
    }
}
