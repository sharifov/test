<?php

namespace modules\order\src\entities\orderData;

/**
* @see OrderData
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byOrderId(int $orderId): self
    {
        return $this->andWhere(['od_order_id' => $orderId]);
    }

    /**
    * @return OrderData[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return OrderData|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
