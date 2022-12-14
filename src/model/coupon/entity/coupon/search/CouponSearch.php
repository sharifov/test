<?php

namespace src\model\coupon\entity\coupon\search;

use common\models\Employee;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use src\model\coupon\entity\coupon\Coupon;

class CouponSearch extends Coupon
{
    public function rules(): array
    {
        return [
            ['c_amount', 'number'],
            ['c_code', 'safe'],

            ['c_created_user_id', 'integer'],
            ['c_currency_code', 'safe'],

            ['c_disabled', 'boolean'],

            ['c_id', 'integer'],
            ['c_percent', 'integer'],

            ['c_public', 'boolean'],

            ['c_reusable', 'boolean'],

            [['c_reusable_count', 'c_used_count'], 'integer'],

            ['c_status_id', 'integer'],
            ['c_type_id', 'integer'],

            ['c_updated_user_id', 'integer'],

            [['c_created_dt', 'c_exp_date', 'c_start_date', 'c_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['c_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->c_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'c_created_dt', $this->c_created_dt, $user->timezone);
        }

        if ($this->c_exp_date) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'c_exp_date', $this->c_exp_date, $user->timezone);
        }

        if ($this->c_start_date) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'c_start_date', $this->c_start_date, $user->timezone);
        }

        if ($this->c_updated_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'c_updated_dt', $this->c_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'c_id' => $this->c_id,
            'c_amount' => $this->c_amount,
            'c_percent' => $this->c_percent,
            'c_reusable' => $this->c_reusable,
            'c_reusable_count' => $this->c_reusable_count,
            'c_public' => $this->c_public,
            'c_status_id' => $this->c_status_id,
            'c_disabled' => $this->c_disabled,
            'c_type_id' => $this->c_type_id,
            'c_created_user_id' => $this->c_created_user_id,
            'c_updated_user_id' => $this->c_updated_user_id,
            'c_currency_code' => $this->c_currency_code,
            'c_code' => $this->c_code,
            'c_used_count' => $this->c_used_count,
        ]);

        return $dataProvider;
    }
}
