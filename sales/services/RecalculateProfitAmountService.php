<?php

namespace sales\services;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;

/**
 * Class ProductQuoteStatusLogService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property OfferRepository $offerRepository
 * @property OrderRepository $orderRepository
 * @property ProductQuote $productQuote
 *
 * @property Offer[] $offers
 * @property Offer[] $changedOffers
 * @property Order[] $orders
 * @property Order[] $changedOrders
 */
class RecalculateProfitAmountService
{
    /**
	 * @var TransactionManager
	 */
	private $transactionManager;
	/**
	 * @var ProductQuoteRepository
	 */
	private $productQuoteRepository;
    /**
	 * @var OfferRepository
	 */
	private $offerRepository;
	/**
	 * @var OrderRepository
	 */
	private $orderRepository;

    private $productQuote;
    private $offers = [];
    private $orders = [];

    public $changedOffers = [];
    public $changedOrders = [];

    /**
     * RecalculateProfitAmountService constructor.
     * @param ProductQuoteRepository $productQuoteRepository
     * @param TransactionManager $transactionManager
     * @param OfferRepository $offerRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        OfferRepository $offerRepository,
        OrderRepository $orderRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->offerRepository = $offerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param ProductQuote $productQuote
     * @param float|null $profitNew
     * @param float|null $profitOld
     * @return RecalculateProfitAmountService
     */
    public function recalculateByProductQuote(ProductQuote $productQuote, ?float $profitNew, ?float $profitOld): RecalculateProfitAmountService
    {
        /* TODO:: removal candidates
            $profitNew
            $profitOld
            Repositories
         */

        $this->productQuote = $productQuote;
        $this->offers = $this->productQuote->opOffers;
        $this->orders = $this->productQuote->orpOrders;

        $this->saveProductQuote();
        $this->recalculateOffer()->saveOffers();
        $this->recalculateOrder()->saveOrders();

        return $this;
    }

    /**
     * @param array $offers [Offer]
     * @return array
     */
    public function recalculateByOffer(array $offers): array
    {
        $this->offers = $offers;
        return $this->recalculateOffer()->saveOffers();
    }

    /**
     * @param array $orders
     * @return array
     */
    public function recalculateByOrder(array $orders): array
    {
        $this->orders[] = $orders;
        return $this->recalculateOrder()->saveOrders();
    }

    /**
     * @return RecalculateProfitAmountService
     */
    private function recalculateOffer(): RecalculateProfitAmountService
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
     */
    private function recalculateOrder(): RecalculateProfitAmountService
    {
        foreach ($this->orders as $order) {
            $order->profitAmount();
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
