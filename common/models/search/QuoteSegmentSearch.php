<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteSegment;

/**
 * QuoteSegmentSearch represents the model behind the search form of `common\models\QuoteSegment`.
 */
class QuoteSegmentSearch extends QuoteSegment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qs_id', 'qs_stop', 'qs_duration', 'qs_mileage', 'qs_cabin_basic', 'qs_trip_id', 'qs_updated_user_id', 'qs_ticket_id', 'qs_recheck_baggage'], 'integer'],
            [['qs_departure_time', 'qs_arrival_time', 'qs_flight_number', 'qs_booking_class', 'qs_departure_airport_code', 'qs_departure_airport_terminal', 'qs_arrival_airport_code', 'qs_arrival_airport_terminal', 'qs_operating_airline', 'qs_marketing_airline', 'qs_air_equip_type', 'qs_marriage_group', 'qs_cabin', 'qs_meal', 'qs_fare_code', 'qs_key', 'qs_created_dt', 'qs_updated_dt'], 'safe'],
        ];
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
        $query = QuoteSegment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['qs_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qs_id' => $this->qs_id,
            'qs_departure_time' => $this->qs_departure_time,
            'qs_arrival_time' => $this->qs_arrival_time,
            'qs_stop' => $this->qs_stop,
            'qs_duration' => $this->qs_duration,
            'qs_mileage' => $this->qs_mileage,
            'qs_cabin_basic' => $this->qs_cabin_basic,
            'qs_trip_id' => $this->qs_trip_id,
            'qs_created_dt' => $this->qs_created_dt,
            'qs_updated_dt' => $this->qs_updated_dt,
            'qs_updated_user_id' => $this->qs_updated_user_id,
            'qs_ticket_id' => $this->qs_ticket_id,
            'qs_recheck_baggage' => $this->qs_recheck_baggage,
        ]);

        $query->andFilterWhere(['like', 'qs_flight_number', $this->qs_flight_number])
            ->andFilterWhere(['like', 'qs_booking_class', $this->qs_booking_class])
            ->andFilterWhere(['like', 'qs_departure_airport_code', $this->qs_departure_airport_code])
            ->andFilterWhere(['like', 'qs_departure_airport_terminal', $this->qs_departure_airport_terminal])
            ->andFilterWhere(['like', 'qs_arrival_airport_code', $this->qs_arrival_airport_code])
            ->andFilterWhere(['like', 'qs_arrival_airport_terminal', $this->qs_arrival_airport_terminal])
            ->andFilterWhere(['like', 'qs_operating_airline', $this->qs_operating_airline])
            ->andFilterWhere(['like', 'qs_marketing_airline', $this->qs_marketing_airline])
            ->andFilterWhere(['like', 'qs_air_equip_type', $this->qs_air_equip_type])
            ->andFilterWhere(['like', 'qs_marriage_group', $this->qs_marriage_group])
            ->andFilterWhere(['like', 'qs_cabin', $this->qs_cabin])
            ->andFilterWhere(['like', 'qs_meal', $this->qs_meal])
            ->andFilterWhere(['like', 'qs_fare_code', $this->qs_fare_code])
            ->andFilterWhere(['like', 'qs_key', $this->qs_key]);

        return $dataProvider;
    }
}
