<?php

namespace modules\flight\src\entities\flightQuoteOption\search;

use yii\data\ActiveDataProvider;
use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;

class FlightQuoteOptionSearch extends FlightQuoteOption
{
    public function rules(): array
    {
        return [
            ['fqo_created_dt', 'safe'],

            ['fqo_updated_dt', 'safe'],

            ['fqo_base_price', 'number'],

            ['fqo_client_total', 'number'],

            ['fqo_display_name', 'safe'],

            ['fqo_flight_pax_id', 'integer'],

            ['fqo_flight_quote_segment_id', 'integer'],

            ['fqo_flight_quote_trip_id', 'integer'],

            ['fqo_id', 'integer'],

            ['fqo_markup_amount', 'number'],

            ['fqo_product_quote_option_id', 'integer'],

            ['fqo_total_price', 'number'],
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
            'fqo_id' => $this->fqo_id,
            'fqo_product_quote_option_id' => $this->fqo_product_quote_option_id,
            'fqo_flight_pax_id' => $this->fqo_flight_pax_id,
            'fqo_flight_quote_segment_id' => $this->fqo_flight_quote_segment_id,
            'fqo_flight_quote_trip_id' => $this->fqo_flight_quote_trip_id,
            'fqo_markup_amount' => $this->fqo_markup_amount,
            'fqo_base_price' => $this->fqo_base_price,
            'fqo_total_price' => $this->fqo_total_price,
            'fqo_client_total' => $this->fqo_client_total,
            'date(fqo_created_dt)' => $this->fqo_created_dt,
            'date(fqo_updated_dt)' => $this->fqo_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'fqo_display_name', $this->fqo_display_name]);

        return $dataProvider;
    }
}
