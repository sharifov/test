<?php

namespace sales\model\coupon\entity\couponCase\search;

use yii\data\ActiveDataProvider;
use sales\model\coupon\entity\couponCase\CouponCase;

class CouponCaseSearch extends CouponCase
{
    public function rules(): array
    {
        return [
            ['cc_case_id', 'integer'],

            ['cc_coupon_id', 'integer'],

            ['cc_created_dt', 'safe'],

            ['cc_created_user_id', 'integer'],

            ['cc_sale_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cc_coupon_id' => $this->cc_coupon_id,
            'cc_case_id' => $this->cc_case_id,
            'cc_sale_id' => $this->cc_sale_id,
            'cc_created_dt' => $this->cc_created_dt,
            'cc_created_user_id' => $this->cc_created_user_id,
        ]);

        return $dataProvider;
    }
}
