<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use webapi\src\logger\behaviors\filters\Filterable;

class V2 implements Filterable
{
    public function filterData($data)
    {
        if (!isset($data['Request']['Card'])) {
            return $data;
        }

        if (!is_array($data['Request']['Card'])) {
            return $data;
        }

        foreach ($data['Request']['Card'] as $key => $value) {
            $data['Request']['Card'][$key] = '***';
        }

        return $data;
    }
}
