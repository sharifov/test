<?php

namespace modules\cruise\src\entity\cruise\search;

use yii\data\ActiveDataProvider;
use modules\cruise\src\entity\cruise\Cruise;

class CruiseSearch extends Cruise
{
    public function rules(): array
    {
        return [
            ['crs_arrival_date_to', 'safe'],

            ['crs_departure_date_from', 'safe'],

            ['crs_destination_code', 'safe'],

            ['crs_destination_label', 'safe'],

            ['crs_id', 'integer'],

            ['crs_product_id', 'integer'],
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
            'crs_id' => $this->crs_id,
            'crs_product_id' => $this->crs_product_id,
            'crs_departure_date_from' => $this->crs_departure_date_from,
            'crs_arrival_date_to' => $this->crs_arrival_date_to,
        ]);

        $query->andFilterWhere(['like', 'crs_destination_code', $this->crs_destination_code])
            ->andFilterWhere(['like', 'crs_destination_label', $this->crs_destination_label]);

        return $dataProvider;
    }
}
