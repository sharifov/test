<?php

namespace webapi\src\logger\behaviors\filters;

/**
 * Class LoggerDataFilter
 */
class LoggerDataFilter implements Filterable
{
    public function filterData($data): array
    {
        return $data;
    }
}
