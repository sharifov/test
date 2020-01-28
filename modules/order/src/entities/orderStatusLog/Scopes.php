<?php

namespace modules\order\src\entities\orderStatusLog;

use yii\db\ActiveQuery;

/**
 * @see OrderStatusLog
 */
class Scopes extends ActiveQuery
{
    public function last(int $orderId): self
    {
        return $this
            ->andWhere(['orsl_order_id' => $orderId])
            ->orderBy(['orsl_id' => SORT_DESC])
            ->limit(1);
    }
}
