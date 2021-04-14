<?php

namespace modules\flight\models\search;

use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteTicket;

class FlightQuoteTicketSearch extends FlightQuoteTicket
{
    public function rules(): array
    {
        return [
            ['fqt_fqb_id', 'integer'],
            ['fqt_pax_id', 'integer'],
            ['fqt_ticket_number', 'string'],
            [['fqt_created_dt', 'fqt_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fqt_updated_dt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fqt_pax_id' => $this->fqt_pax_id,
            'DATE(fqt_created_dt)' => $this->fqt_created_dt,
            'DATE(fqt_updated_dt)' => $this->fqt_updated_dt,
            'fqt_fqb_id' => $this->fqt_fqb_id,
        ]);

        $query->andFilterWhere(['like', 'fqt_ticket_number', $this->fqt_ticket_number]);

        return $dataProvider;
    }
}
