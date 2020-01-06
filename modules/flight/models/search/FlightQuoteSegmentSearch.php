<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteSegment;

/**
 * FlightQuoteSegmentSearch represents the model behind the search form of `modules\flight\models\FlightQuoteSegment`.
 */
class FlightQuoteSegmentSearch extends FlightQuoteSegment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqs_id', 'fqs_flight_quote_id', 'fqs_flight_quote_trip_id', 'fqs_stop', 'fqs_flight_number', 'fqs_duration', 'fqs_ticket_id', 'fqs_recheck_baggage', 'fqs_mileage'], 'integer'],
            [['fqs_departure_dt', 'fqs_arrival_dt', 'fqs_booking_class', 'fqs_departure_airport_iata', 'fqs_departure_airport_terminal', 'fqs_arrival_airport_iata', 'fqs_arrival_airport_terminal', 'fqs_operating_airline', 'fqs_marketing_airline', 'fqs_air_equip_type', 'fqs_marriage_group', 'fqs_cabin_class', 'fqs_meal', 'fqs_fare_code', 'fqs_key'], 'safe'],
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
        $query = FlightQuoteSegment::find();

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
            'fqs_id' => $this->fqs_id,
            'fqs_flight_quote_id' => $this->fqs_flight_quote_id,
            'fqs_flight_quote_trip_id' => $this->fqs_flight_quote_trip_id,
            'fqs_departure_dt' => $this->fqs_departure_dt,
            'fqs_arrival_dt' => $this->fqs_arrival_dt,
            'fqs_stop' => $this->fqs_stop,
            'fqs_flight_number' => $this->fqs_flight_number,
            'fqs_duration' => $this->fqs_duration,
            'fqs_ticket_id' => $this->fqs_ticket_id,
            'fqs_recheck_baggage' => $this->fqs_recheck_baggage,
            'fqs_mileage' => $this->fqs_mileage,
        ]);

        $query->andFilterWhere(['like', 'fqs_booking_class', $this->fqs_booking_class])
            ->andFilterWhere(['like', 'fqs_departure_airport_iata', $this->fqs_departure_airport_iata])
            ->andFilterWhere(['like', 'fqs_departure_airport_terminal', $this->fqs_departure_airport_terminal])
            ->andFilterWhere(['like', 'fqs_arrival_airport_iata', $this->fqs_arrival_airport_iata])
            ->andFilterWhere(['like', 'fqs_arrival_airport_terminal', $this->fqs_arrival_airport_terminal])
            ->andFilterWhere(['like', 'fqs_operating_airline', $this->fqs_operating_airline])
            ->andFilterWhere(['like', 'fqs_marketing_airline', $this->fqs_marketing_airline])
            ->andFilterWhere(['like', 'fqs_air_equip_type', $this->fqs_air_equip_type])
            ->andFilterWhere(['like', 'fqs_marriage_group', $this->fqs_marriage_group])
            ->andFilterWhere(['like', 'fqs_cabin_class', $this->fqs_cabin_class])
            ->andFilterWhere(['like', 'fqs_meal', $this->fqs_meal])
            ->andFilterWhere(['like', 'fqs_fare_code', $this->fqs_fare_code])
            ->andFilterWhere(['like', 'fqs_key', $this->fqs_key]);

        return $dataProvider;
    }
}
