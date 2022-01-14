<?php

namespace src\services\badges\objects;

use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\search\OrderQSearch;
use src\services\badges\BadgeCounterInterface;

/**
 * Class OrderBadgeCounter
 */
class OrderBadgeCounter implements BadgeCounterInterface
{
    public function countTypes(array $types): array
    {
        $result = [];
        $searchModel = new OrderQSearch();

        foreach ($types as $type) {
            switch ($type) {
                case 'new':
                    if ($count = $searchModel->ordersCounter(OrderStatus::NEW)) {
                        $result['new'] = $count;
                    }
                    break;
                case 'pending':
                    if ($count = $searchModel->ordersCounter(OrderStatus::PENDING)) {
                        $result['pending'] = $count;
                    }
                    break;
                case 'processing':
                    if ($count = $searchModel->ordersCounter(OrderStatus::PROCESSING)) {
                        $result['processing'] = $count;
                    }
                    break;
                case 'prepared':
                    if ($count = $searchModel->ordersCounter(OrderStatus::PREPARED)) {
                        $result['prepared'] = $count;
                    }
                    break;
                case 'complete':
                    if ($count = $searchModel->ordersCounter(OrderStatus::COMPLETE)) {
                        $result['complete'] = $count;
                    }
                    break;
                case 'cancel-processing':
                    if ($count = $searchModel->ordersCounter(OrderStatus::CANCEL_PROCESSING)) {
                        $result['cancel-processing'] = $count;
                    }
                    break;
                case 'error':
                    if ($count = $searchModel->ordersCounter(OrderStatus::ERROR)) {
                        $result['error'] = $count;
                    }
                    break;
                case 'declined':
                    if ($count = $searchModel->ordersCounter(OrderStatus::DECLINED)) {
                        $result['declined'] = $count;
                    }
                    break;
                case 'canceled':
                    if ($count = $searchModel->ordersCounter(OrderStatus::CANCELED)) {
                        $result['canceled'] = $count;
                    }
                    break;
                case 'canceled-failed':
                    if ($count = $searchModel->ordersCounter(OrderStatus::CANCEL_FAILED)) {
                        $result['canceled-failed'] = $count;
                    }
                    break;
            }
        }
        return $result;
    }
}
