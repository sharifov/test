<?php

namespace modules\attraction\models\search;

use modules\attraction\models\Attraction;
use yii\data\ActiveDataProvider;

class AttractionSearch extends Attraction
{
    public function search()
    {
        $query = Attraction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
