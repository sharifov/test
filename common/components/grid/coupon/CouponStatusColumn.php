<?php

namespace common\components\grid\coupon;

use sales\model\coupon\entity\coupon\CouponStatus;
use yii\grid\DataColumn;

class CouponStatusColumn extends DataColumn
{
    public $attribute = 'c_status_id';
    public $filter;
    public $format = 'couponStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CouponStatus::getList();
        }
    }
}
