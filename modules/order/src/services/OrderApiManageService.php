<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\api\ProductQuotesForm;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\RecalculateProfitAmountService;
use sales\services\TransactionManager;

/**
 * Class OrderApiManageService
 * @package modules\order\src\services
 *
 * @property OrderRepository $orderRepository
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property TransactionManager $transactionManager
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 * @property ProductQuoteRepository $productQuoteRepository
 */
class OrderApiManageService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var OrderUserProfitRepository
     */
    private $orderUserProfitRepository;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var RecalculateProfitAmountService
     */
    private $recalculateProfitAmountService;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderUserProfitRepository $orderUserProfitRepository,
        RecalculateProfitAmountService $recalculateProfitAmountService,
        TransactionManager $transactionManager,
        ProductQuoteRepository $productQuoteRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderUserProfitRepository = $orderUserProfitRepository;
        $this->transactionManager = $transactionManager;
        $this->recalculateProfitAmountService = $recalculateProfitAmountService;
        $this->productQuoteRepository = $productQuoteRepository;
    }

    /**
     * @param CreateOrderDTO $dto
     * @param ProductQuotesForm[] $productQuotesForms
     * @return Order
     * @throws \Throwable
     */
    public function createOrder(CreateOrderDTO $dto, array $productQuotesForms): Order
    {
        return $this->transactionManager->wrap(function () use ($dto, $productQuotesForms) {
            $newOrder = (new Order())->create($dto);
            $newOrder->processing();
            $orderId = $this->orderRepository->save($newOrder);
            $this->recalculateProfitAmountService->setOrders([$newOrder])->recalculateOrders();

            $newOrderUserProfit = (new OrderUserProfit())->create($orderId, $newOrder->or_owner_user_id, 100, $newOrder->or_profit_amount);
            $this->orderUserProfitRepository->save($newOrderUserProfit);

            foreach ($productQuotesForms as $productQuote) {
                $quote = ProductQuote::findByGid($productQuote->gid);
                $quote->setOrderRelation($newOrder->or_id);
                $this->productQuoteRepository->save($quote);
            }

            return $newOrder;
        });
    }
}
