<?php

namespace sales\model\caseOrder\entity;

use modules\order\src\entities\order\Order;

class CaseOrderQuery
{
    public static function getRelationByOrderId(int $id): CaseOrder
    {
        return CaseOrder::find()->where(['co_order_id' => $id])->one();
    }

    public static function getOrderByCase(int $caseId): ?Order
    {
        return Order::find()
            ->select(Order::tableName() . '.*')
            ->innerJoin(CaseOrder::tableName(), 'or_id = co_order_id AND co_case_id = :case_id', ['case_id' => $caseId])
            ->orderBy(['or_id' => SORT_DESC])
            ->one();
    }
}
