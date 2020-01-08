<?php

namespace webapi\src\logger;

/**
 * Class TechnicalData
 *
 * @property $key
 * @property $data
 */
class TechnicalData
{
    private $key;
    private $data;

    public function __construct(string $key, $data)
    {
        $this->key = $key;
        $this->data = $data;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getData()
    {
        return $this->data;
    }
}
