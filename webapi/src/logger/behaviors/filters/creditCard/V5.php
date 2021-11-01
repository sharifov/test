<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use webapi\src\logger\behaviors\filters\Filterable;

class V5 implements Filterable
{
    public function filterData($data)
    {
        if (!isset($data['payment']['card'])) {
            return $data;
        }
        if (!is_array($data['payment']['card'])) {
            return $data;
        }
        foreach ($data['payment']['card'] as $key => $value) {
            $data['payment']['card'][$key] = '***';
        }

        if (!isset($data['billing'])) {
            return $data;
        }
        if (!is_array($data['billing'])) {
            return $data;
        }
        foreach ($data['billing'] as $key => $value) {
            $data['billing'][$key] = '***';
        }
        return $data;
    }
}
