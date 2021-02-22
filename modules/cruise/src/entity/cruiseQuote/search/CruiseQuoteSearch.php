<?php

namespace modules\cruise\src\entity\cruiseQuote\search;

use yii\data\ActiveDataProvider;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;

class CruiseQuoteSearch extends CruiseQuote
{
    public function rules(): array
    {
        return [
            ['crq_cruise_id', 'integer'],

            ['crq_data_json', 'safe'],

            ['crq_hash_key', 'safe'],

            ['crq_id', 'integer'],

            ['crq_product_quote_id', 'integer'],
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
            'crq_id' => $this->crq_id,
            'crq_product_quote_id' => $this->crq_product_quote_id,
            'crq_cruise_id' => $this->crq_cruise_id,
        ]);

        $query->andFilterWhere(['like', 'crq_hash_key', $this->crq_hash_key])
            ->andFilterWhere(['like', 'crq_data_json', $this->crq_data_json]);

        return $dataProvider;
    }
    public function searchProduct($params): ActiveDataProvider
    {
        $query = static::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'crq_cruise_id' => $this->crq_cruise_id,
        ]);

        $query->innerJoinWith('productQuote')->with('productQuote');

//        $query->andFilterWhere(['like', 'hq_hash_key', $this->hq_hash_key])
//            ->andFilterWhere(['like', 'hq_destination_name', $this->hq_destination_name])
//            ->andFilterWhere(['like', 'hq_hotel_name', $this->hq_hotel_name]);

        return $dataProvider;
    }
}
