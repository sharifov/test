<?php

namespace modules\attraction\src\helpers;

/**
 * Class AttractionQuotePriceDataDTO
 * @package modules\attraction\src\helpers
 *
 * @property AttractionQuotePaxPriceDataDTO[] $prices
 * @property AttractionQuoteTotalPriceDTO $total
 * @property $serviceFeePercent float
 * @property $serviceFee float;
 * @property $processingFee float
 */
class AttractionQuotePriceDataDTO
{
    /**
     * @var $prices AttractionQuotePaxPriceDataDTO[]
     */
    public $prices;

    /**
     * @var AttractionQuoteTotalPriceDTO
     */
    public $total;

    /**
     * @var float
     */
    public $serviceFeePercent;

    /**
     * @var float
     */
    public $serviceFee;

    /**
     * @var float
     */
    public $processingFee;
}
