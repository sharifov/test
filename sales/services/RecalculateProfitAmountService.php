<?php

namespace sales\services;

use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\entities\order\OrderRepository;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class ProductQuoteStatusLogService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property OfferRepository $offerRepository
 * @property OrderRepository $orderRepository
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

    private $productQuoteId;
    private $saveChanges;

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
     * @param bool $saveChanges
     * @return RecalculateProfitAmountService
     */
    public function recalculate(int $productQuoteId, bool $saveChanges = true): RecalculateProfitAmountService
    {
        /* TODO::  */
        $this->productQuoteId = $productQuoteId;
        $this->saveChanges = $saveChanges;



        if ($this->saveChanges) {
            $this->saveProductQuote();
        }
        return $this;
    }

    /**
     * @return RecalculateProfitAmountService
     */
    private function recalculateOffer(): RecalculateProfitAmountService
    {
        /* TODO::  */
        return $this;
    }

    /**
     * @return RecalculateProfitAmountService
     */
    private function recalculateOrder(): RecalculateProfitAmountService
    {
        /* TODO::  */
        return $this;
    }

    /**
     * @return RecalculateProfitAmountService
     */
    private function saveProductQuote(): RecalculateProfitAmountService
    {
        /* TODO:: add transaction manager */

        $productQuote = $this->productQuoteRepository->find($this->productQuoteId);
        $this->productQuoteRepository->save($productQuote);
        return $this;
    }


}
