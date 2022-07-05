<?php

namespace src\traits;

/**
 * Trait InstantiateTrait
 * @package src\traits
 *
 */
trait InstantiateTrait
{
    public static function newInstance(array $attributes = [])
    {
        $model = new static();
        if (!empty($attributes)) {
            $model->fill($attributes);
        }

        return $model;
    }

    public function fill(array $attributes)
    {
        if (method_exists($this, 'load')) {
            $this->load($attributes, '');
        }
        return $this;
    }
}
