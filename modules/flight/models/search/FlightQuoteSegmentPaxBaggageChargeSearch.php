<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;

/**
 * FlightQuoteSegmentPaxBaggageChargeSearch represents the model behind the search form of `modules\flight\models\FlightQuoteSegmentPaxBaggageCharge`.
 */
class FlightQuoteSegmentPaxBaggageChargeSearch extends FlightQuoteSegmentPaxBaggageCharge
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsbc_id', 'qsbc_flight_pax_id', 'qsbc_flight_quote_segment_id', 'qsbc_first_piece', 'qsbc_last_piece'], 'integer'],
            [['qsbc_origin_price', 'qsbc_price', 'qsbc_client_price'], 'number'],
            [['qsbc_origin_currency', 'qsbc_client_currency', 'qsbc_max_weight', 'qsbc_max_size'], 'safe'],
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
        $query = FlightQuoteSegmentPaxBaggageCharge::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['qsbc_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qsbc_id' => $this->qsbc_id,
            'qsbc_flight_pax_id' => $this->qsbc_flight_pax_id,
            'qsbc_flight_quote_segment_id' => $this->qsbc_flight_quote_segment_id,
            'qsbc_first_piece' => $this->qsbc_first_piece,
            'qsbc_last_piece' => $this->qsbc_last_piece,
            'qsbc_origin_price' => $this->qsbc_origin_price,
            'qsbc_price' => $this->qsbc_price,
            'qsbc_client_price' => $this->qsbc_client_price,
        ]);

        $query->andFilterWhere(['like', 'qsbc_origin_currency', $this->qsbc_origin_currency])
            ->andFilterWhere(['like', 'qsbc_client_currency', $this->qsbc_client_currency])
            ->andFilterWhere(['like', 'qsbc_max_weight', $this->qsbc_max_weight])
            ->andFilterWhere(['like', 'qsbc_max_size', $this->qsbc_max_size]);

        return $dataProvider;
    }
}
