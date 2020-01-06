<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuotePaxPrice;

/**
 * FlightQuotePaxPriceSearch represents the model behind the search form of `modules\flight\models\FlightQuotePaxPrice`.
 */
class FlightQuotePaxPriceSearch extends FlightQuotePaxPrice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qpp_id', 'qpp_flight_quote_id', 'qpp_flight_pax_code_id'], 'integer'],
            [['qpp_fare', 'qpp_tax', 'qpp_system_mark_up', 'qpp_agent_mark_up', 'qpp_origin_fare', 'qpp_origin_tax', 'qpp_client_fare', 'qpp_client_tax'], 'number'],
            [['qpp_origin_currency', 'qpp_client_currency', 'qpp_created_dt', 'qpp_updated_dt'], 'safe'],
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
        $query = FlightQuotePaxPrice::find();

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
            'qpp_id' => $this->qpp_id,
            'qpp_flight_quote_id' => $this->qpp_flight_quote_id,
            'qpp_flight_pax_code_id' => $this->qpp_flight_pax_code_id,
            'qpp_fare' => $this->qpp_fare,
            'qpp_tax' => $this->qpp_tax,
            'qpp_system_mark_up' => $this->qpp_system_mark_up,
            'qpp_agent_mark_up' => $this->qpp_agent_mark_up,
            'qpp_origin_fare' => $this->qpp_origin_fare,
            'qpp_origin_tax' => $this->qpp_origin_tax,
            'qpp_client_fare' => $this->qpp_client_fare,
            'qpp_client_tax' => $this->qpp_client_tax,
            'qpp_created_dt' => $this->qpp_created_dt,
            'qpp_updated_dt' => $this->qpp_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'qpp_origin_currency', $this->qpp_origin_currency])
            ->andFilterWhere(['like', 'qpp_client_currency', $this->qpp_client_currency]);

        return $dataProvider;
    }
}
