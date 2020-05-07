<?php

namespace common\components\grid\coupon;

use sales\model\coupon\entity\coupon\CouponType;
use yii\grid\DataColumn;

class CouponTypeColumn extends DataColumn
{
    public $attribute  = 'c_type_id';
    public $filter;
    public $format = 'couponType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CouponType::getList();
        }
    }
}
