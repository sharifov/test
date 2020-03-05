<?php
namespace modules\order\src\services;

use common\models\UserProductType;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\OrderUserProfitFormComposite;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\dispatchers\EventDispatcher;
use sales\model\user\entity\profit\event\UserProfitCalculateByOrderUserProfitEvent;
use sales\model\user\entity\profit\UserProfit;
use sales\model\user\entity\userProductType\UserProductTypeRepository;
use sales\repositories\user\UserProfitRepository;
use sales\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class OrderUserProfitService
 * @package modules\order\src\services
 *
 * @property UserProfitRepository $userProfitRepository
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property UserProductTypeRepository $userProductTypeRepository
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
	/**
	 * @var UserProductTypeRepository
	 */
	private $userProductTypeRepository;

	public function __construct(
		UserProfitRepository $userProfitRepository,
		OrderUserProfitRepository $orderUserProfitRepository,
		UserProductTypeRepository $userProductTypeRepository,
		TransactionManager $transactionManager,
		EventDispatcher $eventDispatcher
	){
		$this->userProfitRepository = $userProfitRepository;
		$this->orderUserProfitRepository = $orderUserProfitRepository;
		$this->transactionManager = $transactionManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->userProductTypeRepository = $userProductTypeRepository;
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
					$row->oup_amount = $order->or_profit_amount;
					$this->orderUserProfitRepository->save($row);
				}

				$this->eventDispatcher->dispatch((new UserProfitCalculateByOrderUserProfitEvent($order)));
			}
		});
	}
}