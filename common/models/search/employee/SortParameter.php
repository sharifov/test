<?php

namespace common\models\search\employee;

use common\models\query\EmployeeQuery;

interface SortParameter
{
    public function apply(EmployeeQuery $query): void;

    public function getSortPriority(): int;
}
