<?php

namespace src\model\coupon\entity\couponUse;

use yii\data\ActiveDataProvider;
use src\model\coupon\entity\couponUse\CouponUse;

class CouponUseSearch extends CouponUse
{
    public function rules(): array
    {
        return [
            ['cu_coupon_id', 'integer'],

            [['cu_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cu_id', 'integer'],

            ['cu_ip', 'safe'],

            ['cu_user_agent', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cu_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cu_id' => $this->cu_id,
            'cu_coupon_id' => $this->cu_coupon_id,
            'DATE(cu_created_dt)' => $this->cu_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cu_ip', $this->cu_ip])
            ->andFilterWhere(['like', 'cu_user_agent', $this->cu_user_agent]);

        return $dataProvider;
    }
}
