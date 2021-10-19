<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

class TotalTicketCalculatedValuesDTO
{
    public float $airlinePenalty = 0.0;

    public float $processingFee = 0.0;

    public float $refundAmount = 0.0;
}
