<?php
namespace modules\order\src\services;


use modules\order\src\entities\orderTips\OrderTips;
use modules\order\src\entities\orderTips\OrderTipsRepository;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfitRepository;
use sales\services\TransactionManager;

/**
 * Class OrderTipsManageService
 * @package modules\order\src\services
 *
 * @property OrderTipsRepository $orderTipsRepository
 * @property TransactionManager $transactionManager
 * @property OrderTipsUserProfitRepository $orderTipsUserProfitRepository
 */
class OrderTipsManageService
{
	/**
	 * @var OrderTipsRepository
	 */
	private $orderTipsRepository;
	/**
	 * @var TransactionManager
	 */
	private $transactionManager;
	/**
	 * @var OrderTipsUserProfitRepository
	 */
	private $orderTipsUserProfitRepository;

	public function __construct(OrderTipsRepository $orderTipsRepository, OrderTipsUserProfitRepository $orderTipsUserProfitRepository, TransactionManager $transactionManager)
	{
		$this->orderTipsRepository = $orderTipsRepository;
		$this->transactionManager = $transactionManager;
		$this->orderTipsUserProfitRepository = $orderTipsUserProfitRepository;
	}

	public function create(OrderTips $orderTips): void
	{
		$this->transactionManager->wrap(function () use ($orderTips) {
			$newOrderTips = $this->orderTipsRepository->save($orderTips);

			if ($newOrderTips->otOrder->or_owner_user_id) {
				$newOrderTipsUserProfit = OrderTipsUserProfit::create($newOrderTips->ot_order_id, $newOrderTips->otOrder->or_owner_user_id, 100, $orderTips->ot_user_profit);
				$this->orderTipsUserProfitRepository->save($newOrderTipsUserProfit);
			}
		});
	}

	public function update(OrderTips $orderTips): void
	{
		$this->transactionManager->wrap(function () use ($orderTips) {
			$this->orderTipsRepository->save($orderTips);

			$orderTipsUserProfit = $this->orderTipsUserProfitRepository->findByOrderId($orderTips->ot_order_id);
			foreach ($orderTipsUserProfit as $tipsUserProfit) {
				$tipsUserProfit->otup_amount = $orderTips->ot_user_profit;
				$this->orderTipsUserProfitRepository->save($tipsUserProfit);
			}
		});
	}
}