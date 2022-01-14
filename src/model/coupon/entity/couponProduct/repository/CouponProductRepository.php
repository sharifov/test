<?php

namespace src\model\coupon\entity\couponProduct\repository;

use src\model\coupon\entity\couponProduct\CouponProduct;
use src\repositories\AbstractBaseRepository;

/**
 * Class CouponProductRepository
 */
class CouponProductRepository extends AbstractBaseRepository
{
    /**
     * @param CouponProduct $model
     */
    public function __construct(CouponProduct $model)
    {
        parent::__construct($model);
    }

    public function getModel(): CouponProduct
    {
        return $this->model;
    }
}
