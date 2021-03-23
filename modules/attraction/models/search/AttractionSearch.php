<?php

namespace modules\attraction\models\search;

use modules\attraction\models\Attraction;
use yii\data\ActiveDataProvider;

class AttractionSearch extends Attraction
{
    public function rules()
    {
        return [
            [['atn_product_id'], 'integer'],

            [['atn_date_from', 'atn_date_to'], 'safe'],
            [['atn_destination'], 'string', 'max' => 100],
            [['atn_destination_code'], 'string', 'max' => 10],

            [['atn_request_hash_key'], 'string', 'max' => 32],
        ];
    }

    public function search($params)
    {
        $query = Attraction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'atn_id' => $this->atn_id,
            'atn_product_id' => $this->atn_product_id,
            'atn_date_from' => $this->atn_date_from,
            'atn_date_to' => $this->atn_date_to,
            'atn_request_hash_key' => $this->atn_request_hash_key
        ]);

        $query->andFilterWhere(['like', 'atn_destination', $this->atn_destination]);
        $query->andFilterWhere(['like', 'atn_destination_code', $this->atn_destination_code]);

        return $dataProvider;
    }
}
