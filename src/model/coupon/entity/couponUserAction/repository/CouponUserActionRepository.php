<?php

namespace src\model\coupon\entity\couponUserAction\repository;

use src\model\coupon\entity\couponUserAction\CouponUserAction;
use src\repositories\AbstractBaseRepository;

/**
 * Class CouponUserActionRepository
 */
class CouponUserActionRepository extends AbstractBaseRepository
{
    /**
     * @param CouponUserAction $model
     */
    public function __construct(CouponUserAction $model)
    {
        parent::__construct($model);
    }

    public function getModel(): CouponUserAction
    {
        return $this->model;
    }
}
