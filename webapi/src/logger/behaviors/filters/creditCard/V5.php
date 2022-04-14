<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use webapi\src\logger\behaviors\filters\Filterable;

class V5 implements Filterable
{
    public function filterData($data)
    {
        if (isset($data['payment']['card']) && is_array($data['payment']['card'])) {
            foreach ($data['payment']['card'] as $key => $value) {
                if (in_array($key, ['cvv', 'number'], true)) {
                    unset($data['payment']['card'][$key]);
                } else {
                    $data['payment']['card'][$key] = CreditCardFilter::replaceSource($value);
                }
            }
        }

        if (isset($data['billing']) && is_array($data['billing'])) {
            foreach ($data['billing'] as $key => $value) {
                $data['billing'][$key] = CreditCardFilter::replaceSource($value);
            }
        }
        return $data;
    }
}
