<?php

namespace src\model\coupon\entity\coupon\serializer;

use src\entities\serializer\Serializer;
use src\model\coupon\entity\coupon\Coupon;
use src\model\coupon\entity\coupon\CouponStatus;
use src\model\coupon\entity\coupon\CouponType;
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

    public function getDataExcept(array $exceptFields = []): array
    {
        $data = $this->getData();
        foreach ($exceptFields as $keyField) {
            if (ArrayHelper::keyExists($keyField, $data)) {
                ArrayHelper::remove($data, $keyField);
            }
        }
        return $data;
    }
}
