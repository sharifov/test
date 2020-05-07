<?php

namespace sales\model\coupon\entity\couponCase\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\coupon\entity\couponCase\CouponCase;

class CouponCaseSearch extends CouponCase
{
    public function rules(): array
    {
        return [
            ['cc_case_id', 'integer'],

            ['cc_coupon_id', 'integer'],

            ['cc_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cc_created_user_id', 'integer'],

            ['cc_sale_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cc_created_dt' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cc_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cc_created_dt', $this->cc_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cc_coupon_id' => $this->cc_coupon_id,
            'cc_case_id' => $this->cc_case_id,
            'cc_sale_id' => $this->cc_sale_id,
            'cc_created_user_id' => $this->cc_created_user_id,
        ]);

        return $dataProvider;
    }
}
