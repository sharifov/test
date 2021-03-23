<?php

namespace modules\order\src\payment\services;

class DummyPaymentService implements PaymentService
{
    public function capture(array $data): array
    {
        return ['transaction_id' => 111111];
    }

    public function refund(array $data): array
    {
        return ['transaction_id' => 111111];
    }
}
