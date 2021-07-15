<?php

namespace sales\model\coupon\entity\coupon\repository;

use sales\model\coupon\entity\coupon\Coupon;
use sales\repositories\AbstractBaseRepository;

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
}
