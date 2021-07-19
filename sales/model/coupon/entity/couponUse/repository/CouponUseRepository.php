<?php

namespace sales\model\coupon\entity\couponUse\repository;

use sales\model\coupon\entity\couponUse\CouponUse;
use sales\repositories\AbstractBaseRepository;

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
