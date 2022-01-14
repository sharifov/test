<?php

namespace src\model\coupon\entity\couponSend;

use yii\data\ActiveDataProvider;
use src\model\coupon\entity\couponSend\CouponSend;

class CouponSendSearch extends CouponSend
{
    public function rules(): array
    {
        return [
            ['cus_coupon_id', 'integer'],

            [['cus_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cus_id', 'integer'],

            ['cus_send_to', 'safe'],

            ['cus_type_id', 'integer'],

            ['cus_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cus_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cus_id' => $this->cus_id,
            'cus_coupon_id' => $this->cus_coupon_id,
            'cus_user_id' => $this->cus_user_id,
            'cus_type_id' => $this->cus_type_id,
            'DATE(cus_created_dt)' => $this->cus_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cus_send_to', $this->cus_send_to]);

        return $dataProvider;
    }
}
