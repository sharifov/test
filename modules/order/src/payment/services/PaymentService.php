<?php

namespace modules\order\src\payment\services;

interface PaymentService
{
    public function capture(array $data): array;
    public function refund(array $data): array;
}
