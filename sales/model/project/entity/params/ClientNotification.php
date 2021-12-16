<?php

namespace sales\model\project\entity\params;

use sales\model\client\notifications\client\entity\NotificationType;

/**
 * Class ClientNotification
 *
 * @property ClientNotificationObject $productQuoteChangeAutoDecisionPendingEvent
 */
class ClientNotification
{
    /**
     * @see property name nust equal name of NotificationType::LIST[NotificationType::PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT]
     */
    public ClientNotificationObject $productQuoteChangeAutoDecisionPendingEvent;

    public function __construct(array $params)
    {
        if (array_key_exists('productQuoteChangeAutoDecisionPendingEvent', $params) && is_array($params['productQuoteChangeAutoDecisionPendingEvent'])) {
            $this->productQuoteChangeAutoDecisionPendingEvent = new ClientNotificationObject($params['productQuoteChangeAutoDecisionPendingEvent']);
        } else {
            $this->productQuoteChangeAutoDecisionPendingEvent = new ClientNotificationObject([]);
        }
    }

    public function typeExist(string $type): bool
    {
        return property_exists($this, $type);
    }
}
