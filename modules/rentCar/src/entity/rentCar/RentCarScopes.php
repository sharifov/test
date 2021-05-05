<?php

namespace modules\rentCar\src\entity\rentCar;

/**
* @see RentCar
*/
class RentCarScopes extends \yii\db\ActiveQuery
{
    /**
    * @return RentCar[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return RentCar|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byProduct(int $productId): RentCarScopes
    {
        return $this->andWhere(['prc_product_id' => $productId]);
    }
}
