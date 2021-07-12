<?php

namespace sales\model\coupon\entity\couponClient;

use yii\data\ActiveDataProvider;
use sales\model\coupon\entity\couponClient\CouponClient;

/**
 * Class CouponClientSearch
 */
class CouponClientSearch extends CouponClient
{
    public function rules(): array
    {
        return [
            [['cuc_id', 'cuc_client_id', 'cuc_coupon_id'], 'integer'],

            [['cuc_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cuc_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cuc_id' => $this->cuc_id,
            'cuc_coupon_id' => $this->cuc_coupon_id,
            'cuc_client_id' => $this->cuc_client_id,
            'DATE(cuc_created_dt)' => $this->cuc_created_dt,
        ]);

        return $dataProvider;
    }
}
