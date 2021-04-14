<?php

namespace modules\flight\models\search;

use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteBooking;

class FlightQuoteBookingSearch extends FlightQuoteBooking
{
    public function rules(): array
    {
        return [
            ['fqb_fqf_id', 'integer'],
            ['fqb_id', 'integer'],

            ['fqb_gds', 'string'],
            ['fqb_gds_pcc', 'string'],
            ['fqb_booking_id', 'string'],
            ['fqb_pnr', 'string'],
            ['fqb_validating_carrier', 'string'],

            [['fqb_created_dt', 'fqb_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fqb_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fqb_id' => $this->fqb_id,
            'fqb_fqf_id' => $this->fqb_fqf_id,
            'DATE(fqb_created_dt)' => $this->fqb_created_dt,
            'DATE(fqb_updated_dt)' => $this->fqb_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'fqb_booking_id', $this->fqb_booking_id])
            ->andFilterWhere(['like', 'fqb_pnr', $this->fqb_pnr])
            ->andFilterWhere(['like', 'fqb_gds', $this->fqb_gds])
            ->andFilterWhere(['like', 'fqb_gds_pcc', $this->fqb_gds_pcc])
            ->andFilterWhere(['like', 'fqb_validating_carrier', $this->fqb_validating_carrier]);

        return $dataProvider;
    }
}
