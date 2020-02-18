<?php
namespace modules\order\src\services;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\OrderForm;
use sales\services\TransactionManager;

/**
 * Class OrderManageService
 * @package modules\order\src\services
 *
 * @property OrderRepository $orderRepository
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property TransactionManager $transactionManager
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

	public function __construct(OrderRepository $orderRepository, OrderUserProfitRepository $orderUserProfitRepository, TransactionManager $transactionManager)
	{
		$this->orderRepository = $orderRepository;
		$this->orderUserProfitRepository = $orderUserProfitRepository;
		$this->transactionManager = $transactionManager;
	}

	/**
	 * @param OrderForm $orderForm
	 * @throws \Throwable
	 */
	public function createOrder(OrderForm $orderForm): void
	{
		$this->transactionManager->wrap(function () use ($orderForm) {
			$newOrder = (new Order)->create($orderForm);
			$orderId = $this->orderRepository->save($newOrder);

			$newOrderUserProfit = (new OrderUserProfit())->create($orderId, $newOrder->or_owner_user_id, 100);
			$this->orderUserProfitRepository->save($newOrderUserProfit);
		});
	}
}