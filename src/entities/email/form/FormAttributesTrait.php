<?php

namespace src\entities\email\form;

/**
 * Trait FormAttributesTrait
 *
 */
trait FormAttributesTrait
{
    public function getAttributesForModel($skipEmpty = false): array
    {
        $result = [];
        foreach ($this->fields() as $index => $name) {
            $key = is_int($index) ? $name : $index;
            if (!$skipEmpty || !$this->isEmptyVal($this->$name)) {
                $result[$key] = $this->$name;
            }
        }
        return $result;
    }

    public function isEmptyVal($val): bool
    {
        return  !isset($val) ||
            $val == null ||
            (is_array($val) && count($val) == 0) ||
            (is_string($val) && strlen($val) == 0);
    }

    public function isEmpty(): bool
    {
        foreach ($this->attributes() as $attribute) {
            if (!empty($this->$attribute)) {
                return false;
            }
        }
        return true;
    }
}
