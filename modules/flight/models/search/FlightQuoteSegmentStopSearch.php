<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteSegmentStop;

/**
 * FlightQuoteSegmentStopSearch represents the model behind the search form of `modules\flight\models\FlightQuoteSegmentStop`.
 */
class FlightQuoteSegmentStopSearch extends FlightQuoteSegmentStop
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qss_id', 'qss_quote_segment_id', 'qss_elapsed_time', 'qss_duration'], 'integer'],
            [['qss_location_iata', 'qss_equipment', 'qss_departure_dt', 'qss_arrival_dt'], 'safe'],
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
        $query = FlightQuoteSegmentStop::find();

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
            'qss_id' => $this->qss_id,
            'qss_quote_segment_id' => $this->qss_quote_segment_id,
            'qss_elapsed_time' => $this->qss_elapsed_time,
            'qss_duration' => $this->qss_duration,
            'qss_departure_dt' => $this->qss_departure_dt,
            'qss_arrival_dt' => $this->qss_arrival_dt,
        ]);

        $query->andFilterWhere(['like', 'qss_location_iata', $this->qss_location_iata])
            ->andFilterWhere(['like', 'qss_equipment', $this->qss_equipment]);

        return $dataProvider;
    }
}
