<?php

namespace sales\model\caseOrder\entity;

class CaseOrderQuery
{
    public static function getRelationByOrderId(int $id): CaseOrder
    {
        return CaseOrder::find()->where(['co_order_id' => $id])->one();
    }
}
