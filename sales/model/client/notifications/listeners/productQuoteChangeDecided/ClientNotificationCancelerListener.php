<?php

namespace sales\model\client\notifications\listeners\productQuoteChangeDecided;

use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionable;
use sales\model\client\notifications\client\entity\NotificationType;
use sales\model\client\notifications\ClientNotificationCanceler;

/**
 * Class ClientNotificationCancelerListener
 *
 * @property ClientNotificationCanceler $canceler
 */
class ClientNotificationCancelerListener
{
    private ClientNotificationCanceler $canceler;

    public function __construct(ClientNotificationCanceler $canceler)
    {
        $this->canceler = $canceler;
    }

    public function handle(ProductQuoteChangeDecisionable $event): void
    {
        $this->canceler->cancel(NotificationType::PRODUCT_QUOTE_CHANGE_CREATED_EVENT, $event->getId());
    }
}
