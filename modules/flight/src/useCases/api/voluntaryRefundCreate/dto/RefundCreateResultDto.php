<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate\dto;

/**
 * Class RefundCreateResultDto
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate\dto
 *
 * @property array $boSaleData
 * @property array $boRefundData
 */
class RefundCreateResultDto
{
    public array $boSaleData;
    public array $boRefundData;

    public function __construct(
        array $boSaleData,
        array $boRefundData
    ) {
        $this->boSaleData = $boSaleData;
        $this->boRefundData = $boRefundData;
    }
}
