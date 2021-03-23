<?php

namespace modules\product\src\entities\productType;

use modules\product\src\entities\product\Product;

/**
 * @see ProductType
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['pt_enabled' => true]);
    }

    public function byHotel(): self
    {
        return $this->byType(ProductType::PRODUCT_HOTEL);
    }

    public function byFlight(): self
    {
        return $this->byType(ProductType::PRODUCT_FLIGHT);
    }

    public function byAttraction(): self
    {
        return $this->byType(ProductType::PRODUCT_ATTRACTION);
    }

    public function byRentCar(): self
    {
        return $this->byType(ProductType::PRODUCT_RENT_CAR);
    }

    public function byCruise(): self
    {
        return $this->byType(ProductType::PRODUCT_CRUISE);
    }

    public function byType(int $id): self
    {
        return $this->andWhere(['pt_id' => $id]);
    }
}
