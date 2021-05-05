<?php

namespace modules\order\src\widgets\multipleUpdate\order;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\OrderStatusAction;
use yii\bootstrap4\Html;

/**
 * Class MultipleUpdateService
 *
 * @property array $report
 * @property OrderRepository $orderRepository
 */
class MultipleUpdateService
{
    private $report;
    private OrderRepository $orderRepository;

    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->report = [];
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param MultipleUpdateForm $form
     * @return array
     */
    public function update(MultipleUpdateForm $form): array
    {
        foreach ($form->ids as $orderId) {
            if (!$order = Order::findOne($orderId)) {
                $this->addMessage('Not found Order: ' . $orderId);
                continue;
            }

            $this->changeStatus($order, $form);
        }

        return $this->report;
    }

    private function changeStatus(Order $order, MultipleUpdateForm $form): void
    {
        if ($form->statusId === OrderStatus::PROCESSING) {
            try {
                $order->processing($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::PROCESSING)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::PREPARED) {
            try {
                $order->prepare($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::PREPARED)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::COMPLETE) {
            try {
                $order->complete($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::COMPLETE)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::CANCELED) {
            try {
                $order->cancel($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::CANCELED)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::NEW) {
            try {
                $order->new($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::NEW)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::PENDING) {
            try {
                $order->pending($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::PENDING)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::CANCEL_PROCESSING) {
            try {
                $order->cancelProcessing($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::CANCEL_PROCESSING)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::ERROR) {
            try {
                $order->error($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::ERROR)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } elseif ($form->statusId === OrderStatus::DECLINED) {
            try {
                $order->decline($form->reason, OrderStatusAction::MULTIPLE_UPDATE, $form->authUserId());
                $this->orderRepository->save($order);
                $this->addMessage($this->movedStateMessage($order->or_id, OrderStatus::getName(OrderStatus::DECLINED)));
            } catch (\DomainException $e) {
                $this->addMessage('Order: ' . $order->or_id . ': ' . $e->getMessage());
            }
        } else {
            $this->addMessage('Undefined status: ' . $form->statusId . ' for multi update Order: ' . $order->or_id);
            \Yii::warning('Undefined status: ' . $form->statusId . ' for multi update Order: ' . $order->or_id, 'order\MultipleUpdateService:changeStatus:undefinedStatus:OrderId:' . $order->or_id);
        }
    }

    public function formatReport(array $reports): string
    {
        if (!$reports) {
            return '';
        }

        $out = '<ul>';
        foreach ($reports as $report) {
            $out .= Html::tag('li', Html::tag('span', $report, ['style' => 'color: #28a048']));
        }
        return $out . '</ul>';
    }

    private function addMessage(string $message): void
    {
        $this->report[] = $message;
    }

    private function movedStateMessage($orderId, string $status): string
    {
        $message = '<span style="color: #28a048">Order: ' . $orderId . ' moved to ' . $status;
        return $message . '</span>';
    }
}
