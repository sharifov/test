<?php

namespace modules\order\src\formatter;

use modules\order\src\entities\order\Order;
use sales\logger\formatter\Formatter;

/**
 * Class OrderFormatter
 *
 * @property Order $order
 */
class OrderFormatter implements Formatter
{

    /**
     * @var Order
     */
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getFormattedAttributeLabel(string $attribute): string
    {
        return $this->order->getAttributeLabel($attribute);
    }

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function getFormattedAttributeValue($attribute, $value)
    {
        $functions = $this->getAttributeFormatters();

        if (array_key_exists($attribute, $functions)) {
            return $functions[$attribute]($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getExceptedAttributes(): array
    {
        return [
            'or_created_dt',
            'or_updated_dt',
            'or_created_user_id',
            'or_updated_user_id',
            'or_owner_user_id'
        ];
    }

    /**
     * @return array
     */
    private function getAttributeFormatters(): array
    {
        return [];
    }
}
