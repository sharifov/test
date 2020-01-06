<?php

namespace webapi\src\logger\behaviors\filters;

use sales\helpers\payment\CreditCardHelper;

/**
 * Class CardFilter
 */
class CreditCardFilter implements Filterable
{
    public function filterData($data): array
    {
        if (isset($data['Card']['number']) && is_string($data['Card']['number'])) {
            $data['Card']['number'] = CreditCardHelper::maskCreditCard($data['Card']['number']);
        }
        if (isset($data['Card']['cvv']) && is_string($data['Card']['cvv'])) {
            $data['Card']['cvv'] = CreditCardHelper::maskCreditCard($data['Card']['cvv'], '*', 1, 0);
        }
        return $data;
    }
}
