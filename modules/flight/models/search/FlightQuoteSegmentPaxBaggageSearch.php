<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;

/**
 * FlightQuoteSegmentPaxBaggageSearch represents the model behind the search form of `modules\flight\models\FlightQuoteSegmentPaxBaggage`.
 */
class FlightQuoteSegmentPaxBaggageSearch extends FlightQuoteSegmentPaxBaggage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsb_id', 'qsb_flight_pax_code_id', 'qsb_flight_quote_segment_id', 'qsb_allow_pieces', 'qsb_allow_weight'], 'integer'],
            [['qsb_airline_code', 'qsb_allow_unit', 'qsb_allow_max_weight', 'qsb_allow_max_size'], 'safe'],
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
        $query = FlightQuoteSegmentPaxBaggage::find();

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
            'qsb_id' => $this->qsb_id,
            'qsb_flight_pax_code_id' => $this->qsb_flight_pax_code_id,
            'qsb_flight_quote_segment_id' => $this->qsb_flight_quote_segment_id,
            'qsb_allow_pieces' => $this->qsb_allow_pieces,
            'qsb_allow_weight' => $this->qsb_allow_weight,
        ]);

        $query->andFilterWhere(['like', 'qsb_airline_code', $this->qsb_airline_code])
            ->andFilterWhere(['like', 'qsb_allow_unit', $this->qsb_allow_unit])
            ->andFilterWhere(['like', 'qsb_allow_max_weight', $this->qsb_allow_max_weight])
            ->andFilterWhere(['like', 'qsb_allow_max_size', $this->qsb_allow_max_size]);

        return $dataProvider;
    }
}
