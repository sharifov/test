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
            if (!$skipEmpty || !empty($this->$name)) {
                $result[$key] = $this->$name;
            }
        }
        return $result;
    }
}
