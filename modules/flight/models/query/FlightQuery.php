<?php

namespace modules\flight\models\query;

/**
 * This is the ActiveQuery class for [[\modules\flight\models\Flight]].
 *
 * @see \modules\flight\models\Flight
 */
class FlightQuery extends \yii\db\ActiveQuery
{
    public function byProduct(int $productId): self
    {
        return $this->andWhere(['fl_product_id' => $productId]);
    }
}
