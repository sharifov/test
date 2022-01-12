<?php

namespace src\model\coupon\entity\coupon\repository;

use src\model\coupon\entity\coupon\Coupon;
use src\repositories\AbstractBaseRepository;

/**
 * Class CouponRepository
 */
class CouponRepository extends AbstractBaseRepository
{
    /**
     * @param Coupon $model
     */
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Coupon
     */
    public function getModel(): Coupon
    {
        return $this->model;
    }
}
