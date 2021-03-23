<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use webapi\src\logger\behaviors\filters\Filterable;

class V3 implements Filterable
{
    public function filterData($data)
    {
        if (!isset($data['creditCard'])) {
            return $data;
        }

        if (!is_array($data['creditCard'])) {
            return $data;
        }

        foreach ($data['creditCard'] as $key => $value) {
            $data['creditCard'][$key] = '***';
        }

        return $data;
    }
}
