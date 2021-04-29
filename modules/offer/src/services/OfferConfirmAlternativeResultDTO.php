<?php

namespace modules\offer\src\services;

/**
 * Class OfferConfirmAlternativeResultDTO
 * @package modules\offer\src\services
 *
 * @property int $cntConfirmedQuotes
 * @property ?int $orderId
 */
class OfferConfirmAlternativeResultDTO
{
    public int $cntConfirmedQuotes = 0;

    public ?int $orderId = null;
}
