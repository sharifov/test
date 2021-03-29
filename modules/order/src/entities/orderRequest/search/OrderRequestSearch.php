<?php

namespace modules\order\src\entities\orderRequest\search;

use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderRequest\OrderRequest;

class OrderRequestSearch extends OrderRequest
{
    public function rules(): array
    {
        return [
            ['orr_created_dt', 'safe'],

            ['orr_id', 'integer'],

            ['orr_request_data_json', 'safe'],

            ['orr_response_data_json', 'safe'],

            ['orr_response_type_id', 'integer'],

            ['orr_source_type_id', 'integer'],
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
            'orr_id' => $this->orr_id,
            'orr_source_type_id' => $this->orr_source_type_id,
            'orr_response_type_id' => $this->orr_response_type_id,
            'orr_created_dt' => $this->orr_created_dt,
        ]);

        $query->andFilterWhere(['like', 'orr_request_data_json', $this->orr_request_data_json])
            ->andFilterWhere(['like', 'orr_response_data_json', $this->orr_response_data_json]);

        return $dataProvider;
    }
}
