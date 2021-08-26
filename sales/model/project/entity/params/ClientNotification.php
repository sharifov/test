<?php

namespace sales\model\project\entity\params;

use sales\model\client\notifications\client\entity\NotificationType;

/**
 * Class ClientNotification
 *
 * @property ClientNotificationObject $productQuoteChange
 */
class ClientNotification
{
    /**
     * @see property name nust equal name of NotificationType::PRODUCT_QUOTE_CHANGE
     */
    public ClientNotificationObject $productQuoteChange;

    public function __construct(array $params)
    {
        if (array_key_exists('productQuoteChange', $params) && is_array($params['productQuoteChange'])) {
            $this->productQuoteChange = new ClientNotificationObject($params['productQuoteChange']);
        } else {
            $this->productQuoteChange = new ClientNotificationObject([]);
        }
    }

    public function typeExist(string $type): bool
    {
        return property_exists($this, $type);
    }
}
