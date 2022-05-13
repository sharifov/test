<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteSegmentBaggageCharge;

/**
 * QuoteSegmentBaggageChargeSearch represents the model behind the search form of `common\models\QuoteSegmentBaggageCharge`.
 */
class QuoteSegmentBaggageChargeSearch extends QuoteSegmentBaggageCharge
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsbc_id', 'qsbc_segment_id', 'qsbc_first_piece', 'qsbc_last_piece', 'qsbc_updated_user_id'], 'integer'],
            [['qsbc_pax_code', 'qsbc_currency', 'qsbc_max_weight', 'qsbc_max_size', 'qsbc_created_dt', 'qsbc_updated_dt'], 'safe'],
            [['qsbc_price'], 'number'],
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
        $query = QuoteSegmentBaggageCharge::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['qsbc_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qsbc_id' => $this->qsbc_id,
            'qsbc_segment_id' => $this->qsbc_segment_id,
            'qsbc_first_piece' => $this->qsbc_first_piece,
            'qsbc_last_piece' => $this->qsbc_last_piece,
            'qsbc_price' => $this->qsbc_price,
            'qsbc_created_dt' => $this->qsbc_created_dt,
            'qsbc_updated_dt' => $this->qsbc_updated_dt,
            'qsbc_updated_user_id' => $this->qsbc_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'qsbc_pax_code', $this->qsbc_pax_code])
            ->andFilterWhere(['like', 'qsbc_currency', $this->qsbc_currency])
            ->andFilterWhere(['like', 'qsbc_max_weight', $this->qsbc_max_weight])
            ->andFilterWhere(['like', 'qsbc_max_size', $this->qsbc_max_size]);

        return $dataProvider;
    }
}
