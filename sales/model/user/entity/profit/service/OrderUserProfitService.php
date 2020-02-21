<?php
namespace sales\model\user\entity\profit\service;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\OrderUserProfitFormComposite;
use sales\dispatchers\EventDispatcher;
use sales\model\user\entity\profit\event\UserProfitRecalculateEvent;
use sales\model\user\entity\profit\UserProfit;
use sales\repositories\user\UserProfitRepository;
use sales\services\TransactionManager;

/**
 * Class OrderUserProfitService
 * @package sales\model\user\entity\profit\service
 *
 * @property UserProfitRepository $userProfitRepository
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property TransactionManager $transactionManager
 * @property EventDispatcher $eventDispatcher
 */
class OrderUserProfitService
{
	/**
	 * @var UserProfitRepository
	 */
	private $userProfitRepository;
	/**
	 * @var OrderUserProfitRepository
	 */
	private $orderUserProfitRepository;
	/**
	 * @var TransactionManager
	 */
	private $transactionManager;
	/**
	 * @var EventDispatcher
	 */
	private $eventDispatcher;

	public function __construct(UserProfitRepository $userProfitRepository, OrderUserProfitRepository $orderUserProfitRepository, TransactionManager $transactionManager, EventDispatcher $eventDispatcher)
	{
		$this->userProfitRepository = $userProfitRepository;
		$this->orderUserProfitRepository = $orderUserProfitRepository;
		$this->transactionManager = $transactionManager;
		$this->eventDispatcher = $eventDispatcher;
	}

	public function calculateUserProfit(int $productQuoteId, Order $order): void
	{
		foreach ($order->orderUserProfit as $profit) {
			$userProfit = $this->userProfitRepository->findOrCreate($profit->oup_user_id, $profit->oup_order_id, $productQuoteId);
			if ($userProfit->up_id) {
				$userProfit->updateProfit((new OrderUserProfitCreateUpdateDTO(
					null,
					null,
					null,
					null,
					null,
					$order->or_profit_amount,
					$profit->oup_percent
				)));
			} else {
				$userProfit->create((new OrderUserProfitCreateUpdateDTO(
					$profit->oup_user_id,
					$order->or_lead_id,
					$order->or_id,
					$productQuoteId,
					null,
					$order->or_profit_amount,
					$profit->oup_percent,
					UserProfit::STATUS_PENDING
				)));
			}

			$this->userProfitRepository->save($userProfit);
		}
	}

	/**
	 * @param OrderUserProfitFormComposite $form
	 * @param Order $order
	 * @throws \Throwable
	 */
	public function updateMultiple(OrderUserProfitFormComposite $form, Order $order): void
	{
		$this->transactionManager->wrap(function () use ($form, $order) {
			/** @var OrderUserProfit[] $orderUserProfits */
			$orderUserProfits = $form->getAttributeValueByName('orderUserProfits');

			if ($orderUserProfits) {

				$this->orderUserProfitRepository->deleteByOrderId($order->or_id);

				foreach ($orderUserProfits as $row) {
					$row->insert();
					$this->orderUserProfitRepository->save($row);
				}

				$this->eventDispatcher->dispatch((new UserProfitRecalculateEvent($order)));
			}
		});
	}
}