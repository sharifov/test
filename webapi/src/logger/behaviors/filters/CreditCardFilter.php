<?php

namespace webapi\src\logger\behaviors\filters;

use sales\helpers\payment\CreditCardHelper;

/**
 * Class CardFilter
 */
class CardFilter implements Filterable
{
    public function filterData($data): array
    {
        if (isset($data['Card']['number'])) {
            $data['Card']['number'] = CreditCardHelper::maskCreditCard($data['Card']['number']);
        }
        return $data;
    }
}
