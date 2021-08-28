<?php

namespace sales\model\project\entity\params;

use sales\model\client\notifications\client\entity\NotificationType;

/**
 * Class ClientNotification
 *
 * @property ClientNotificationObject $productQuoteChangeCreatedEvent
 */
class ClientNotification
{
    /**
     * @see property name nust equal name of NotificationType::LIST[NotificationType::PRODUCT_QUOTE_CHANGE_CREATED_EVENT]
     */
    public ClientNotificationObject $productQuoteChangeCreatedEvent;

    public function __construct(array $params)
    {
        if (array_key_exists('productQuoteChangeCreatedEvent', $params) && is_array($params['productQuoteChangeCreatedEvent'])) {
            $this->productQuoteChangeCreatedEvent = new ClientNotificationObject($params['productQuoteChangeCreatedEvent']);
        } else {
            $this->productQuoteChangeCreatedEvent = new ClientNotificationObject([]);
        }
    }

    public function typeExist(string $type): bool
    {
        return property_exists($this, $type);
    }
}
