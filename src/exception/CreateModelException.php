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

    public function getFirstErrors()
    {
        if (empty($this->errors)) {
            return [];
        }

        $errors = [];
        foreach ($this->errors as $name => $es) {
            if (!empty($es)) {
                $errors[$name] = reset($es);
            }
        }

        return $errors;
    }

    public function getErrorSummary($showAllErrors)
    {
        $lines = [];
        $errors = $showAllErrors ? $this->getErrors() : $this->getFirstErrors();
        foreach ($errors as $es) {
            $lines = array_merge($lines, (array)$es);
        }
        return $lines;
    }
}
