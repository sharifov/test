<?php

namespace sales\model\coupon\entity\coupon\serializer;

use sales\entities\serializer\Serializer;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\coupon\CouponType;
use yii\helpers\ArrayHelper;

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
            'c_code',
            'c_amount',
            'c_currency_code',
            'c_percent',
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

        $data['startDate'] = $this->model->c_start_date ? date('Y-m-d', strtotime($this->model->c_start_date)) : null;
        $data['expDate'] = $this->model->c_exp_date ? date('Y-m-d', strtotime($this->model->c_exp_date)) : null;
        $data['statusName'] = CouponStatus::getName($this->model->c_status_id);
        $data['typeName'] = CouponType::getName($this->model->c_type_id);

        return $data;
    }

    public function getDataValidate(): array
    {
        $data = $this->toArray();
        $toRemove = [
            'c_code', 'c_amount', 'c_currency_code', 'c_percent', 'c_public', 'c_status_id', 'c_type_id', 'c_created_dt',
        ];

        foreach ($toRemove as $value) {
            ArrayHelper::remove($data, $value);
        }

        $data['startDate'] = $this->model->c_start_date ? date('Y-m-d', strtotime($this->model->c_start_date)) : null;
        $data['expDate'] = $this->model->c_exp_date ? date('Y-m-d', strtotime($this->model->c_exp_date)) : null;
        $data['statusName'] = CouponStatus::getName($this->model->c_status_id);

        return $data;
    }
}
