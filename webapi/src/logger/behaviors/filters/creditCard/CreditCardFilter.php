<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use webapi\src\logger\behaviors\filters\Filterable;

/**
 * Class CreditCardFilter
 *
 * @property Filterable[] $filters
 */
class CreditCardFilter implements Filterable
{
    private array $filters;

    public function __construct()
    {
        $this->filters = [
            new V1(),
            new V2(),
            new V3(),
        ];
    }

    public function filterData($data)
    {
        foreach ($this->filters as $filter) {
            $data = $filter->filterData($data);
        }
        return $data;
    }
}
