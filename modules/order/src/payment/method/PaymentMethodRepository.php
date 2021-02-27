<?php

namespace modules\order\src\payment\method;

use common\models\PaymentMethod;
use sales\repositories\NotFoundException;

class PaymentMethodRepository
{
    public function findByKey(string $key): PaymentMethod
    {
        if ($paymentMethod = PaymentMethod::findOne(['pm_key' => $key])) {
            return $paymentMethod;
        }
        throw new NotFoundException('Payment method not found by key: ' . $key);
    }
}
