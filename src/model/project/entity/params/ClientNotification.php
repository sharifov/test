<?php

namespace src\model\project\entity\params;

use src\model\client\notifications\client\entity\NotificationType;

/**
 * Class ClientNotification
 *
 * @property ClientNotificationObject $productQuoteChangeAutoDecisionPendingEvent
 * @property ClientNotificationObject $productQuoteChangeClientRemainderNotificationEvent
 */
class ClientNotification
{
    /**
     * @see property name nust equal name of NotificationType::LIST[NotificationType::PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT]
     */
    public ClientNotificationObject $productQuoteChangeAutoDecisionPendingEvent;
    public ClientNotificationObject $productQuoteChangeClientRemainderNotificationEvent;

    public function __construct(array $params)
    {
        if (array_key_exists('productQuoteChangeAutoDecisionPendingEvent', $params) && is_array($params['productQuoteChangeAutoDecisionPendingEvent'])) {
            $this->productQuoteChangeAutoDecisionPendingEvent = new ClientNotificationObject($params['productQuoteChangeAutoDecisionPendingEvent']);
        } else {
            $this->productQuoteChangeAutoDecisionPendingEvent = new ClientNotificationObject([]);
        }

        if (array_key_exists('productQuoteChangeClientRemainderNotificationEvent', $params) && is_array($params['productQuoteChangeClientRemainderNotificationEvent'])) {
            $this->productQuoteChangeClientRemainderNotificationEvent = new ClientNotificationObject($params['productQuoteChangeClientRemainderNotificationEvent']);
        } else {
            $this->productQuoteChangeClientRemainderNotificationEvent = new ClientNotificationObject([]);
        }
    }

    public function typeExist(string $type): bool
    {
        return property_exists($this, $type);
    }
}
