<?php

namespace modules\flight\src\entities\flightQuoteTicketRefund\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;

/**
 * FlightQuoteTicketRefundSearch represents the model behind the search form of `modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund`.
 */
class FlightQuoteTicketRefundSearch extends FlightQuoteTicketRefund
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqtr_id', 'fqtr_fqb_id'], 'integer'],
            [['fqtr_ticket_number', 'fqtr_created_dt'], 'safe'],
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
        $query = FlightQuoteTicketRefund::find();

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
            'fqtr_id' => $this->fqtr_id,
            'fqtr_created_dt' => $this->fqtr_created_dt,
            'fqtr_fqb_id' => $this->fqtr_fqb_id,
        ]);

        $query->andFilterWhere(['like', 'fqtr_ticket_number', $this->fqtr_ticket_number]);

        return $dataProvider;
    }
}
