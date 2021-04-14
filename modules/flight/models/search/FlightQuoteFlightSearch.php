<?php

namespace modules\flight\models\search;

use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteFlight;

class FlightQuoteFlightSearch extends FlightQuoteFlight
{
    public function rules(): array
    {
        return [
            ['fqf_booking_id', 'string', 'max' => 50],
            ['fqf_cabin_class', 'string', 'max' => 1],
            ['fqf_main_airline', 'string', 'max' => 2],
            ['fqf_pnr', 'string', 'max' => 10],
            ['fqf_original_data_json', 'string'],
            ['fqf_validating_carrier', 'string', 'max' => 2],

            [['fqf_fare_type_id', 'fqf_fq_id', 'fqf_id', 'fqf_status_id', 'fqf_trip_type_id', 'fqf_type_id'], 'integer'],

            [['fqf_created_dt', 'fqf_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fqf_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fqf_id' => $this->fqf_id,
            'fqf_fq_id' => $this->fqf_fq_id,
            'fqf_type_id' => $this->fqf_type_id,
            'fqf_trip_type_id' => $this->fqf_trip_type_id,
            'fqf_fare_type_id' => $this->fqf_fare_type_id,
            'fqf_status_id' => $this->fqf_status_id,
            'DATE(fqf_created_dt)' => $this->fqf_created_dt,
            'DATE(fqf_updated_dt)' => $this->fqf_updated_dt,
        ]);

        $query
            ->andFilterWhere(['like', 'fqf_cabin_class', $this->fqf_cabin_class])
            ->andFilterWhere(['like', 'fqf_main_airline', $this->fqf_main_airline])
            ->andFilterWhere(['like', 'fqf_booking_id', $this->fqf_booking_id])
            ->andFilterWhere(['like', 'fqf_pnr', $this->fqf_pnr])
            ->andFilterWhere(['like', 'fqf_validating_carrier', $this->fqf_validating_carrier])
            ->andFilterWhere(['like', 'fqf_original_data_json', $this->fqf_original_data_json]);

        return $dataProvider;
    }
}
