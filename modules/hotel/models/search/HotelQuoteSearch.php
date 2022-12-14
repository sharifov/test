<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelQuote;

/**
 * HotelQuoteSearch represents the model behind the search form of `modules\hotel\models\HotelQuote`.
 */
class HotelQuoteSearch extends HotelQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hq_id', 'hq_hotel_id', 'hq_product_quote_id', 'hq_hotel_list_id'], 'integer'],
            [['hq_request_hash', 'hq_booking_id'], 'string'],
            [['hq_hash_key', 'hq_destination_name', 'hq_hotel_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = HotelQuote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'hq_id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'hq_id' => $this->hq_id,
            'hq_hotel_id' => $this->hq_hotel_id,
            'hq_product_quote_id' => $this->hq_product_quote_id,
            'hq_hotel_list_id' => $this->hq_hotel_list_id,
            'hq_request_hash' => $this->hq_request_hash,
        ]);

        $query->andFilterWhere(['like', 'hq_hash_key', $this->hq_hash_key])
            ->andFilterWhere(['like', 'hq_destination_name', $this->hq_destination_name])
            ->andFilterWhere(['like', 'hq_booking_id', $this->hq_booking_id])
            ->andFilterWhere(['like', 'hq_hotel_name', $this->hq_hotel_name]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchProduct($params): ActiveDataProvider
    {
        $query = HotelQuote::find();

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
            'hq_id' => $this->hq_id,
            'hq_hotel_id' => $this->hq_hotel_id,
            'hq_product_quote_id' => $this->hq_product_quote_id,
            'hq_hotel_list_id' => $this->hq_hotel_list_id,
        ]);

        $query->innerJoinWith('hqProductQuote')->with('hqProductQuote');

//        $query->andFilterWhere(['like', 'hq_hash_key', $this->hq_hash_key])
//            ->andFilterWhere(['like', 'hq_destination_name', $this->hq_destination_name])
//            ->andFilterWhere(['like', 'hq_hotel_name', $this->hq_hotel_name]);

        return $dataProvider;
    }
}
