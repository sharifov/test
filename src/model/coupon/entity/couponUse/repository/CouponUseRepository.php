<?php

namespace src\model\coupon\entity\couponUse\repository;

use src\model\coupon\entity\couponUse\CouponUse;
use src\repositories\AbstractBaseRepository;

/**
 * Class CouponUseRepository
 */
class CouponUseRepository extends AbstractBaseRepository
{
    /**
     * @param CouponUse $model
     */
    public function __construct(CouponUse $model)
    {
        parent::__construct($model);
    }

    public function getModel(): CouponUse
    {
        return $this->model;
    }
}
