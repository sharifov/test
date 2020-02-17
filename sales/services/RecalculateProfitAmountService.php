<?php

namespace sales\services;

use modules\offer\src\entities\offer\Offer;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class RecalculateProfitAmountService
 * @property ProductQuote $productQuote
 *
 * @property Offer[] $offers
 * @property Offer[] $changedOffers
 * @property Order[] $orders
 * @property Order[] $changedOrders
 */
class RecalculateProfitAmountService
{
    private $productQuote;
    private $offers = [];
    private $orders = [];

    public $changedOffers = [];
    public $changedOrders = [];

    /**
     * @param ProductQuote $productQuote
     * @return RecalculateProfitAmountService
     */
    public function setByProductQuote(ProductQuote $productQuote): RecalculateProfitAmountService
    {
        $this->productQuote = $productQuote;
        $this->offers = $this->productQuote->opOffers;
        $this->orders = $this->productQuote->orpOrders;

        return $this;
    }

    /**
     * @return RecalculateProfitAmountService
     * @throws \yii\base\InvalidConfigException
     */
    public function recalculateAll(): RecalculateProfitAmountService
    {
        $this->saveProductQuote();
        $this->setChangedOffers()->saveOffers();
        $this->setChangedOrders()->saveOrders();

        return $this;
    }

    /**
     * @param array $offers
     * @return RecalculateProfitAmountService
     */
    public function setOffers(array $offers): RecalculateProfitAmountService
    {
        $this->offers = $offers;
        return $this;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function recalculateOffers(): array
    {
        return $this->setChangedOffers()->saveOffers();
    }

    /**
     * @param array $orders
     * @return RecalculateProfitAmountService
     */
    public function setOrders(array $orders): RecalculateProfitAmountService
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function recalculateOrders(): array
    {
        return $this->setChangedOrders()->saveOrders();
    }

    /**
     * @return RecalculateProfitAmountService
     * @throws \yii\base\InvalidConfigException
     */
    private function setChangedOffers(): RecalculateProfitAmountService
    {
        foreach ($this->offers as $offer) {
            if ($offer->profitAmount()) {
                $this->changedOffers[] = $offer;
            }
        }
        return $this;
    }

    /**
     * @return RecalculateProfitAmountService
     * @throws \yii\base\InvalidConfigException
     */
    private function setChangedOrders(): RecalculateProfitAmountService
    {
        foreach ($this->orders as $order) {
            if ($order->profitAmount()) {
                $this->changedOrders[] = $order;
            }
        }
        return $this;
    }

    /**
     * @return int
     */
    public function saveProductQuote(): int
    {
        if (!$this->productQuote->save(false)) {
            throw new \RuntimeException('Product Quote not saved');
        }
        return $this->productQuote->pq_id;
    }

    /**
     * @return array
     */
    public function saveOffers(): array
    {
        $result = [];
        foreach ($this->changedOffers as $offer) {
            $saved = $offer->save(false);
            if ($saved) {
                $result[] = $offer->of_id;
            } else {
                throw new \RuntimeException('Offer not saved');
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function saveOrders(): array
    {
        $result = [];
        foreach ($this->changedOrders as $order) {
            $saved = $order->save(false);
            if ($saved) {
                $result[] = $order->or_id;
            } else {
                throw new \RuntimeException('Order not saved');
            }
        }
        return $result;
    }
}
