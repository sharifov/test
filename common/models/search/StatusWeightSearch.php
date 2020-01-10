<?php

namespace common\models\search;

use yii\data\ActiveDataProvider;
use common\models\StatusWeight;

/**
 * Class StatusWeightSearch
 */
class StatusWeightSearch extends StatusWeight
{
    public function rules(): array
    {
        return [
            ['sw_status_id', 'integer'],
            ['sw_weight', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = StatusWeight::find();

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
            'sw_status_id' => $this->sw_status_id,
            'sw_weight' => $this->sw_weight,
        ]);

        return $dataProvider;
    }
}
