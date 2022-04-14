<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use webapi\src\logger\behaviors\filters\Filterable;

class V4 implements Filterable
{
    public function filterData($data)
    {
        if (!isset($data['payment_request']['method_data']['card'])) {
            return $data;
        }
        if (!is_array($data['payment_request']['method_data']['card'])) {
            return $data;
        }
        foreach ($data['payment_request']['method_data']['card'] as $key => $value) {
            if (in_array($key, ['cvv', 'number'], true)) {
                unset($data['payment_request']['method_data']['card'][$key]);
            } else {
                $data['payment_request']['method_data']['card'][$key] = CreditCardFilter::replaceSource($value);
            }
        }
        return $data;
    }
}
