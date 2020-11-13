<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Airports;

/**
 * AirportsSearch represents the model behind the search form of `common\models\Airports`.
 */
class AirportsSearch extends Airports
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city', 'country', 'iata', 'timezone', 'a_icao', 'a_country_code', 'a_city_code', 'a_state'], 'safe'],
            [['latitude', 'longitude', 'a_rank'], 'number'],
            [['dst', 'a_created_user_id', 'a_updated_user_id', 'a_multicity', 'a_close', 'a_disabled'], 'integer'],
            [['a_created_dt', 'a_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Airports::find();

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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'dst' => $this->dst,
            'a_created_user_id' => $this->a_created_user_id,
            'a_updated_user_id' => $this->a_updated_user_id,
            'a_rank' => $this->a_rank,
            'a_multicity' => $this->a_multicity,
            'a_close' => $this->a_close,
            'a_disabled' => $this->a_disabled,
            'DATE(a_created_dt)' => $this->a_created_dt,
            'DATE(a_updated_dt)' => $this->a_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'iata', $this->iata])
            ->andFilterWhere(['like', 'timezone', $this->timezone])
            ->andFilterWhere(['like', 'a_icao', $this->a_icao])
            ->andFilterWhere(['like', 'a_country_code', $this->a_country_code])
            ->andFilterWhere(['like', 'a_city_code', $this->a_city_code])
            ->andFilterWhere(['like', 'a_state', $this->a_state]);

        return $dataProvider;
    }
}
