<?php

namespace modules\order\src\payment\services;

interface PaymentService
{
    public function void(array $data): void;
    public function capture(array $data): array;
    public function refund(array $data): void;
}
