<?php

namespace modules\order\src\entities\orderData\search;

use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderData\OrderData;

class OrderDataSearch extends OrderData
{
    public function rules(): array
    {
        return [
            ['od_created_by', 'integer'],

            ['od_created_dt', 'safe'],

            ['od_display_uid', 'safe'],

            ['od_id', 'integer'],

            ['od_order_id', 'integer'],

            ['od_source_id', 'integer'],

            ['od_updated_by', 'integer'],

            ['od_updated_dt', 'safe'],
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
            'od_id' => $this->od_id,
            'od_order_id' => $this->od_order_id,
            'od_created_by' => $this->od_created_by,
            'od_updated_by' => $this->od_updated_by,
            'date(od_created_dt)' => $this->od_created_dt,
            'date(od_updated_dt)' => $this->od_updated_dt,
            'od_source_id' => $this->od_source_id,
        ]);

        $query->andFilterWhere(['like', 'od_display_uid', $this->od_display_uid]);

        return $dataProvider;
    }
}
