<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteSegmentStop;

/**
 * QuoteSegmentStopSearch represents the model behind the search form of `common\models\QuoteSegmentStop`.
 */
class QuoteSegmentStopSearch extends QuoteSegmentStop
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qss_id', 'qss_duration', 'qss_elapsed_time', 'qss_segment_id'], 'integer'],
            [['qss_location_code', 'qss_departure_dt', 'qss_arrival_dt', 'qss_equipment'], 'safe'],
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
        $query = QuoteSegmentStop::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['qss_id' => SORT_DESC]]
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
            'qss_departure_dt' => $this->qss_departure_dt,
            'qss_arrival_dt' => $this->qss_arrival_dt,
            'qss_duration' => $this->qss_duration,
            'qss_elapsed_time' => $this->qss_elapsed_time,
            'qss_segment_id' => $this->qss_segment_id,
        ]);

        $query->andFilterWhere(['like', 'qss_location_code', $this->qss_location_code])
            ->andFilterWhere(['like', 'qss_equipment', $this->qss_equipment]);

        return $dataProvider;
    }
}
