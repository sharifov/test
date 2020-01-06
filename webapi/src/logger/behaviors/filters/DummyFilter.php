<?php

namespace webapi\src\logger\behaviors\filters;

/**
 * Class DummyFilter
 */
class DummyFilter implements Filterable
{
    public function filterData($data): array
    {
        return $data;
    }
}
