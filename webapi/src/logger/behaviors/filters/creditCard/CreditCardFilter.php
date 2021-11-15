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
            new V4(),
            new V5(),
        ];
    }

    public function filterData($data)
    {
        foreach ($this->filters as $filter) {
            $data = $filter->filterData($data);
        }
        return $data;
    }

    /**
     * @param mixed $source
     * @param string $substitute
     * @return mixed
     */
    public static function replaceSource($source, string $substitute = '*')
    {
        if (!is_string($source)) {
            return $source;
        }
        return str_repeat($substitute, strlen($source));
    }
}
