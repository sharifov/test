<?php

namespace src\exception;

class CreateModelException extends \RuntimeException
{
    protected $errors = [];

    public function __construct(string $className = '', $errors = [])
    {
        $this->errors = $errors;
        $message = sprintf('%s create error.', empty($className) ? 'Model' : $className);
        parent::__construct($message);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
