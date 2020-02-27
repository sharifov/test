<?php
namespace modules\order\src\services;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\OrderForm;
use sales\services\RecalculateProfitAmountService;
use sales\services\TransactionManager;

/**
 * Class OrderManageService
 * @package modules\order\src\services
 *
 * @property OrderRepository $orderRepository
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property TransactionManager $transactionManager
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 */
class OrderManageService
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

	public function __construct(OrderRepository $orderRepository, OrderUserProfitRepository $orderUserProfitRepository, RecalculateProfitAmountService $recalculateProfitAmountService, TransactionManager $transactionManager)
	{
		$this->orderRepository = $orderRepository;
		$this->orderUserProfitRepository = $orderUserProfitRepository;
		$this->transactionManager = $transactionManager;
		$this->recalculateProfitAmountService = $recalculateProfitAmountService;
	}

	/**
	 * @param CreateOrderDTO $dto
	 * @return Order
	 * @throws \Throwable
	 */
	public function createOrder(CreateOrderDTO $dto): Order
	{
		return $this->transactionManager->wrap(function () use ($dto) {
			$newOrder = (new Order)->create($dto);
			$orderId = $this->orderRepository->save($newOrder);
			$this->recalculateProfitAmountService->setOrders([$newOrder])->recalculateOrders();

			$newOrderUserProfit = (new OrderUserProfit())->create($orderId, $newOrder->or_owner_user_id, 100, $newOrder->or_profit_amount);
			$this->orderUserProfitRepository->save($newOrderUserProfit);

			return $newOrder;
		});
	}
}