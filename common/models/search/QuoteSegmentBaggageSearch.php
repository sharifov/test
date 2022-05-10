<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteSegmentBaggage;

/**
 * QuoteSegmentBaggageSearch represents the model behind the search form of `common\models\QuoteSegmentBaggage`.
 */
class QuoteSegmentBaggageSearch extends QuoteSegmentBaggage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsb_id', 'qsb_segment_id', 'qsb_allow_pieces', 'qsb_allow_weight', 'qsb_updated_user_id', 'qsb_carry_one'], 'integer'],
            [['qsb_pax_code', 'qsb_airline_code', 'qsb_allow_unit', 'qsb_allow_max_weight', 'qsb_allow_max_size', 'qsb_created_dt', 'qsb_updated_dt'], 'safe'],
        ];
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
        $query = QuoteSegmentBaggage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['qsb_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qsb_id' => $this->qsb_id,
            'qsb_segment_id' => $this->qsb_segment_id,
            'qsb_allow_pieces' => $this->qsb_allow_pieces,
            'qsb_allow_weight' => $this->qsb_allow_weight,
            'qsb_created_dt' => $this->qsb_created_dt,
            'qsb_updated_dt' => $this->qsb_updated_dt,
            'qsb_updated_user_id' => $this->qsb_updated_user_id,
            'qsb_carry_one' => $this->qsb_carry_one,
        ]);

        $query->andFilterWhere(['like', 'qsb_pax_code', $this->qsb_pax_code])
            ->andFilterWhere(['like', 'qsb_airline_code', $this->qsb_airline_code])
            ->andFilterWhere(['like', 'qsb_allow_unit', $this->qsb_allow_unit])
            ->andFilterWhere(['like', 'qsb_allow_max_weight', $this->qsb_allow_max_weight])
            ->andFilterWhere(['like', 'qsb_allow_max_size', $this->qsb_allow_max_size]);

        return $dataProvider;
    }
}
