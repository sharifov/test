<?php

namespace modules\order\src\payment\services;

class DummyPaymentService implements PaymentService
{
    public function void(array $data): void
    {
    }

    public function capture(array $data): array
    {
        return ['transaction_id' => 111111];
    }

    public function refund(array $data): void
    {
    }
}
