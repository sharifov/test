<?php

namespace common\components\grid\coupon;

use sales\model\coupon\entity\coupon\CouponStatus;
use yii\grid\DataColumn;

class CouponStatusColumn extends DataColumn
{
    public $filter;
    public $format = 'raw';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CouponStatus::getList();
        }
    }

    public function getDataCellValue($model, $key, $index)
    {
        return CouponStatus::asFormat($model->{$this->attribute});
    }
}
