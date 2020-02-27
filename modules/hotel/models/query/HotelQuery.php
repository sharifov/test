<?php

namespace modules\hotel\models\query;

/**
 * This is the ActiveQuery class for [[\modules\hotel\models\Hotel]].
 *
 * @see \modules\hotel\models\Hotel
 */
class HotelQuery extends \yii\db\ActiveQuery
{
    public function byProduct(int $productId): self
    {
        return $this->andWhere(['ph_product_id' => $productId]);
    }
}
