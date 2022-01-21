<?php

namespace src\traits;

/**
 * Trait FieldsTrait
 *
 * @property array $fields
 */
trait FieldsTrait
{
    public array $fields = [];

    public function fields(): array
    {
        return !empty($this->fields) ? $this->fields : parent::fields();
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
}
