<?php

namespace sales\model\coupon\entity\couponUserAction\repository;

use sales\model\coupon\entity\couponUserAction\CouponUserAction;
use sales\repositories\AbstractBaseRepository;

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
