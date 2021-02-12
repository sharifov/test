<?php

namespace modules\cruise\src\entity\cruiseCabinPax\search;

use yii\data\ActiveDataProvider;
use modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax;

class CruiseCabinPaxSearch extends CruiseCabinPax
{
    public function rules(): array
    {
        return [
            ['crp_age', 'integer'],

            ['crp_cruise_cabin_id', 'integer'],

            ['crp_dob', 'safe'],

            ['crp_first_name', 'safe'],

            ['crp_id', 'integer'],

            ['crp_last_name', 'safe'],

            ['crp_type_id', 'integer'],
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
            'crp_id' => $this->crp_id,
            'crp_cruise_cabin_id' => $this->crp_cruise_cabin_id,
            'crp_type_id' => $this->crp_type_id,
            'crp_age' => $this->crp_age,
            'crp_dob' => $this->crp_dob,
        ]);

        $query->andFilterWhere(['like', 'crp_first_name', $this->crp_first_name])
            ->andFilterWhere(['like', 'crp_last_name', $this->crp_last_name]);

        return $dataProvider;
    }
}
