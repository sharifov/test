<?php

namespace sales\model\coupon\entity\coupon\serializer;

use sales\entities\serializer\Serializer;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\coupon\CouponType;

/**
 * Class CouponSerializer
 *
 * @property Coupon $model
 */
class CouponSerializer extends Serializer
{
    /**
     * @param Coupon $model
     */
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'c_id',
            'c_code',
            'c_amount',
            'c_currency_code',
            'c_percent',
            'c_exp_date',
            'c_start_date',
            'c_reusable',
            'c_reusable_count',
            'c_used_count',
            'c_public',
            'c_status_id',
            'c_disabled',
            'c_type_id',
            'c_created_dt',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();
        $data['statusName'] = CouponStatus::getName($this->model->c_status_id);
        $data['typeName'] = CouponType::getName($this->model->c_type_id);

        return $data;
    }
}
