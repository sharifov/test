<?php

namespace webapi\src\logger\behaviors;

/**
 * Interface Filterable
 */
interface Filterable
{
    public function filterData($data);
}
