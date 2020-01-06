<?php

namespace webapi\src\logger\behaviors\filters;

/**
 * Interface Filterable
 */
interface Filterable
{
    public function filterData($data);
}
