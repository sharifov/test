<?php

namespace sales\services;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\entities\order\OrderRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class ProductQuoteStatusLogService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property OfferRepository $offerRepository
 * @property OrderRepository $orderRepository
 * @property ProductQuote $productQuote
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

    public $productQuote;
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
     * @param int $productQuoteId
     * @param float $profitNew
     * @return RecalculateProfitAmountService
     */
    public function recalculate(int $productQuoteId, float $profitNew): RecalculateProfitAmountService
    {
        $this->productQuote = $this->productQuoteRepository->find($productQuoteId);
        $this->productQuote->pq_profit_amount = $profitNew;

        /* TODO:: add logic if delete productQuote */
        /* TODO:: add Status logic  */

        $this->recalculateOffer();
        $this->recalculateOrder();

        return $this;
    }

    /**
     * @return RecalculateProfitAmountService
     */
    private function recalculateOffer(): RecalculateProfitAmountService
    {
        $offers = $this->productQuote->opOffers;
        foreach ($offers as $offer) {
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
        $orders = $this->productQuote->orpOrders;
        foreach ($orders as $order) {
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
        return $this->productQuoteRepository->save($this->productQuote);
    }

    /**
     * @return array
     */
    public function saveOffers(): array
    {
        $result = [];
        foreach ($this->changedOffers as $offer) {
            $result[] = $this->offerRepository->save($offer);
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
            $result[] = $this->orderRepository->save($order);
        }
        return $result;
    }
}
