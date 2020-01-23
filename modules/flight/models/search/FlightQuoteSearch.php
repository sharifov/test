<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuote;

/**
 * FlightQuoteSearch represents the model behind the search form of `modules\flight\models\FlightQuote`.
 */
class FlightQuoteSearch extends FlightQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fq_id', 'fq_flight_id', 'fq_source_id', 'fq_product_quote_id', 'fq_gds_offer_id', 'fq_type_id', 'fq_trip_type_id', 'fq_fare_type_id', 'fq_created_user_id', 'fq_created_expert_id'], 'integer'],
            [['fq_hash_key', 'fq_record_locator', 'fq_gds', 'fq_gds_pcc', 'fq_cabin_class', 'fq_main_airline', 'fq_created_expert_name', 'fq_reservation_dump', 'fq_pricing_info', 'fq_origin_search_data', 'fq_last_ticket_date', 'fq_request_hash'], 'safe'],
            [['fq_service_fee_percent'], 'number'],
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
        $query = FlightQuote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['fq_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'fq_id' => $this->fq_id,
            'fq_flight_id' => $this->fq_flight_id,
            'fq_source_id' => $this->fq_source_id,
            'fq_product_quote_id' => $this->fq_product_quote_id,
            'fq_service_fee_percent' => $this->fq_service_fee_percent,
            'fq_gds_offer_id' => $this->fq_gds_offer_id,
            'fq_type_id' => $this->fq_type_id,
            'fq_trip_type_id' => $this->fq_trip_type_id,
            'fq_fare_type_id' => $this->fq_fare_type_id,
            'fq_created_user_id' => $this->fq_created_user_id,
            'fq_created_expert_id' => $this->fq_created_expert_id,
            'fq_last_ticket_date' => $this->fq_last_ticket_date,
            'fq_request_hash' => $this->fq_request_hash,
        ]);

        $query->andFilterWhere(['like', 'fq_hash_key', $this->fq_hash_key])
            ->andFilterWhere(['like', 'fq_record_locator', $this->fq_record_locator])
            ->andFilterWhere(['like', 'fq_gds', $this->fq_gds])
            ->andFilterWhere(['like', 'fq_gds_pcc', $this->fq_gds_pcc])
            ->andFilterWhere(['like', 'fq_cabin_class', $this->fq_cabin_class])
            ->andFilterWhere(['like', 'fq_main_airline', $this->fq_main_airline])
            ->andFilterWhere(['like', 'fq_created_expert_name', $this->fq_created_expert_name])
            ->andFilterWhere(['like', 'fq_reservation_dump', $this->fq_reservation_dump])
            ->andFilterWhere(['like', 'fq_pricing_info', $this->fq_pricing_info])
            ->andFilterWhere(['like', 'fq_origin_search_data', $this->fq_origin_search_data]);

        return $dataProvider;
    }
}
