<?php

namespace modules\cruise\src\entity\cruiseCabin\search;

use yii\data\ActiveDataProvider;
use modules\cruise\src\entity\cruiseCabin\CruiseCabin;

class CruiseCabinSearch extends CruiseCabin
{
    public function rules(): array
    {
        return [
            ['crc_cruise_id', 'integer'],

            ['crc_id', 'integer'],

            ['crc_name', 'safe'],
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
            'crc_id' => $this->crc_id,
            'crc_cruise_id' => $this->crc_cruise_id,
        ]);

        $query->andFilterWhere(['like', 'crc_name', $this->crc_name]);

        return $dataProvider;
    }
}
