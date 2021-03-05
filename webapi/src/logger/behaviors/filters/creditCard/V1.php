<?php

namespace webapi\src\logger\behaviors\filters\creditCard;

use sales\helpers\payment\CreditCardHelper;
use webapi\src\logger\behaviors\filters\Filterable;

class V1 implements Filterable
{
    public function filterData($data)
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
