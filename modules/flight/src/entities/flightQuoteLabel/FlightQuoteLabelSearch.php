<?php

namespace modules\flight\src\entities\flightQuoteLabel;

use yii\data\ActiveDataProvider;
use modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel;

class FlightQuoteLabelSearch extends FlightQuoteLabel
{
    public function rules(): array
    {
        return [
            ['fql_label_key', 'string'],

            ['fql_quote_id', 'integer'],
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
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fql_quote_id' => $this->fql_quote_id,
        ]);

        $query->andFilterWhere(['like', 'fql_label_key', $this->fql_label_key]);

        return $dataProvider;
    }
}
