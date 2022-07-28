<?php

namespace modules\product\src\entities\productQuoteChange\events;

/**
 * Class ProductQuoteChangeClientRemainderNotificationEvent
 *
 * @property int $productQuoteChangeId
 */
class ProductQuoteChangeClientRemainderNotificationEvent implements ProductQuoteChangeInterface
{
    public int $productQuoteChangeId;

    public function __construct(int $productQuoteChangeId)
    {
        $this->productQuoteChangeId = $productQuoteChangeId;
    }

    public function getId(): int
    {
        return $this->productQuoteChangeId;
    }

    public function getClass(): string
    {
        return self::class;
    }
}
