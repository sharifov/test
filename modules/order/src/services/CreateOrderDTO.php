<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;

class CreateOrderDTO
{
    public $payStatus;

    public $status;

    public $leadId;

    public $requestData;

    public function __construct(int $leadId, array $requestData = [])
    {
        $this->payStatus = OrderPayStatus::NOT_PAID;
        $this->status = OrderStatus::PENDING;
        $this->leadId = $leadId;
        $this->requestData = $requestData;
    }
}
