<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightSegment;

/**
 * FlightSegmentSearch represents the model behind the search form of `modules\flight\models\FlightSegment`.
 */
class FlightSegmentSearch extends FlightSegment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fs_id', 'fs_flight_id', 'fs_origin_iata', 'fs_flex_type_id', 'fs_flex_days'], 'integer'],
            [['fs_destination_iata', 'fs_departure_date'], 'safe'],
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
        $query = FlightSegment::find();

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
            'fs_id' => $this->fs_id,
            'fs_flight_id' => $this->fs_flight_id,
            'fs_origin_iata' => $this->fs_origin_iata,
            'fs_departure_date' => $this->fs_departure_date,
            'fs_flex_type_id' => $this->fs_flex_type_id,
            'fs_flex_days' => $this->fs_flex_days,
        ]);

        $query->andFilterWhere(['like', 'fs_destination_iata', $this->fs_destination_iata]);

        return $dataProvider;
    }
}
