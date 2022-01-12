<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfitRepository;
use modules\order\src\forms\OrderTipsUserProfitFormComposite;
use src\dispatchers\EventDispatcher;
use src\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use src\model\user\entity\userProductType\UserProductTypeRepository;
use src\repositories\user\UserProfitRepository;
use src\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class OrderTipsUserProfitService
 * @package modules\order\src\services
 *
 * @property UserProfitRepository $userProfitRepository
 * @property OrderTipsUserProfitRepository $orderTipsUserProfitRepository
 * @property UserProductTypeRepository $userProductTypeRepository
 * @property TransactionManager $transactionManager
 * @property EventDispatcher $eventDispatcher
 */
class OrderTipsUserProfitService
{
    /**
     * @var UserProfitRepository
     */
    private $userProfitRepository;
    /**
     * @var OrderTipsUserProfitRepository
     */
    private $orderTipsUserProfitRepository;
    /**
     * @var UserProductTypeRepository
     */
    private $userProductTypeRepository;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(
        UserProfitRepository $userProfitRepository,
        OrderTipsUserProfitRepository $orderTipsUserProfitRepository,
        UserProductTypeRepository $userProductTypeRepository,
        TransactionManager $transactionManager,
        EventDispatcher $eventDispatcher
    ) {
        $this->userProfitRepository = $userProfitRepository;
        $this->orderTipsUserProfitRepository = $orderTipsUserProfitRepository;
        $this->userProductTypeRepository = $userProductTypeRepository;
        $this->transactionManager = $transactionManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param OrderTipsUserProfitFormComposite $form
     * @param Order $order
     * @throws \Throwable
     */
    public function updateMultiple(OrderTipsUserProfitFormComposite $form, Order $order): void
    {
        $this->transactionManager->wrap(function () use ($form, $order) {
            /** @var OrderTipsUserProfit[] $orderTipsUserProfits */
            $orderTipsUserProfits = $form->getAttributeValueByName('orderTipsUserProfits');

            if ($orderTipsUserProfits) {
                $this->orderTipsUserProfitRepository->deleteByOrderId($order->or_id);

                foreach ($orderTipsUserProfits as $row) {
                    $row->scenario = OrderTipsUserProfit::SCENARIO_INSERT;
                    $row->insert();
                    $row->otup_amount = $order->orderTips->ot_user_profit;
                    $this->orderTipsUserProfitRepository->save($row);
                }

                $this->eventDispatcher->dispatch((new UserProfitCalculateByOrderTipsUserProfitsEvent($order)));
            }
        });
    }
}
