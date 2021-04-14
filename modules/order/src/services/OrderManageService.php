<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderData\OrderDataLanguage;
use modules\order\src\entities\orderData\OrderDataMarketCountry;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\OrderForm;
use sales\model\leadOrder\entity\LeadOrder;
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
 * @property OrderDataService $orderDataService
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
    /**
     * @var OrderDataService
     */
    private OrderDataService $orderDataService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderUserProfitRepository $orderUserProfitRepository,
        RecalculateProfitAmountService $recalculateProfitAmountService,
        TransactionManager $transactionManager,
        OrderDataService $orderDataService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderUserProfitRepository = $orderUserProfitRepository;
        $this->transactionManager = $transactionManager;
        $this->recalculateProfitAmountService = $recalculateProfitAmountService;
        $this->orderDataService = $orderDataService;
    }

    /**
     * @param CreateOrderDTO $dto
     * @param int|null $sourceId
     * @param string $action
     * @param int|null $createdUserId
     * @return Order
     * @throws \Throwable
     */
    public function createOrder(CreateOrderDTO $dto, ?int $sourceId, string $action, ?int $createdUserId): Order
    {
        return $this->transactionManager->wrap(function () use ($dto, $sourceId, $action, $createdUserId) {
            $newOrder = (new Order())->create($dto);
            $orderId = $this->orderRepository->save($newOrder);
            $this->recalculateProfitAmountService->setOrders([$newOrder])->recalculateOrders();

            if ($dto->leadId) {
                $leadOrder = new LeadOrder();
                $leadOrder->lo_lead_id = $dto->leadId;
                $leadOrder->lo_order_id = $orderId;
                if (!$leadOrder->save()) {
                    throw new \RuntimeException('Lead order saving failed');
                }
            }

            $newOrderUserProfit = (new OrderUserProfit())->create($orderId, $newOrder->or_owner_user_id, 100, $newOrder->or_profit_amount);
            $this->orderUserProfitRepository->save($newOrderUserProfit);

            $this->orderDataService->create(
                $orderId,
                null,
                $sourceId,
                $dto->languageId,
                $dto->marketCountry,
                $action,
                $createdUserId
            );

            return $newOrder;
        });
    }
}
