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
     * @param int $maxLen
     * @return mixed
     */
    public static function replaceSource($source, string $substitute = '*', int $maxLen = 4)
    {
        if (is_numeric($source)) {
            $source = (string) $source;
        }
        if (!is_string($source)) {
            return $source;
        }
        $length = strlen($source);
        $middle = str_repeat($substitute, $length);
        if ($length <= $maxLen) {
            return $middle;
        }

        $first = $source[0];
        $last = substr($source, -1);

        return $first . $middle . $last;
    }
}
