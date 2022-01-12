<?php

namespace src\model\client\notifications\listeners\productQuoteChangeDecided;

use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionable;
use src\model\client\notifications\client\entity\NotificationType;
use src\model\client\notifications\ClientNotificationCanceler;

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
        $this->canceler->cancel(NotificationType::PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT, $event->getId());
    }
}
