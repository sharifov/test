<?php

namespace sales\model\airline\entity;

use yii\data\ActiveDataProvider;
use common\models\Airline;

class AirlineSearch extends Airline
{
    public function rules(): array
    {
        return [
            [
                [
                    'code', 'iaco', 'countryCode', 'country','cl_economy', 'cl_premium_economy',
                    'cl_business', 'cl_premium_business', 'cl_first', 'cl_premium_first', 'name'
                ],
                'string', 'max' => 255
            ],
            [['iata'], 'string', 'max' => 2],
            ['id', 'integer'],

            ['updated_dt', 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['iata' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'updated_dt' => $this->updated_dt,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'iata', $this->iata])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'iaco', $this->iaco])
            ->andFilterWhere(['like', 'countryCode', $this->countryCode])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'cl_economy', $this->cl_economy])
            ->andFilterWhere(['like', 'cl_premium_economy', $this->cl_premium_economy])
            ->andFilterWhere(['like', 'cl_business', $this->cl_business])
            ->andFilterWhere(['like', 'cl_premium_business', $this->cl_premium_business])
            ->andFilterWhere(['like', 'cl_first', $this->cl_first])
            ->andFilterWhere(['like', 'cl_premium_first', $this->cl_premium_first]);

        return $dataProvider;
    }
}
