<?php

namespace sales\model\coupon\entity\couponProduct;

use yii\data\ActiveDataProvider;
use sales\model\coupon\entity\couponProduct\CouponProduct;

class CouponProductSearch extends CouponProduct
{
    public function rules(): array
    {
        return [
            ['cup_coupon_id', 'integer'],

            ['cup_data_json', 'safe'],

            ['cup_product_type_id', 'integer'],
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
            'cup_coupon_id' => $this->cup_coupon_id,
            'cup_product_type_id' => $this->cup_product_type_id,
        ]);

        $query->andFilterWhere(['like', 'cup_data_json', $this->cup_data_json]);

        return $dataProvider;
    }
}
