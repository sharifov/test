<?php

namespace modules\cruise\src\entity\cruise;

/**
* @see Cruise
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byProduct(int $productId): self
    {
        return $this->andWhere(['crs_product_id' => $productId]);
    }

    /**
    * @return Cruise[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return Cruise|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
