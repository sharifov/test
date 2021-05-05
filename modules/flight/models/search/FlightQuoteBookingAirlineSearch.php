<?php

namespace modules\flight\models\search;

use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteBookingAirline;

class FlightQuoteBookingAirlineSearch extends FlightQuoteBookingAirline
{
    public function rules(): array
    {
        return [
            ['fqba_fqb_id', 'integer'],
            ['fqba_id', 'integer'],

            ['fqba_record_locator', 'string'],
            ['fqba_airline_code', 'string'],

            [['fqba_created_dt', 'fqba_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fqba_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fqba_id' => $this->fqba_id,
            'fqba_fqb_id' => $this->fqba_fqb_id,
            'DATE(fqba_created_dt)' => $this->fqba_created_dt,
            'DATE(fqba_updated_dt)' => $this->fqba_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'fqba_record_locator', $this->fqba_record_locator])
            ->andFilterWhere(['like', 'fqba_airline_code', $this->fqba_airline_code]);

        return $dataProvider;
    }
}
