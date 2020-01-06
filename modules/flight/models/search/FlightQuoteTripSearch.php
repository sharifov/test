<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteTrip;

/**
 * FlightQuoteTripSearch represents the model behind the search form of `modules\flight\models\FlightQuoteTrip`.
 */
class FlightQuoteTripSearch extends FlightQuoteTrip
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqt_id', 'fqt_flight_quote_id', 'fqt_duration'], 'integer'],
            [['fqt_key'], 'safe'],
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
        $query = FlightQuoteTrip::find();

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
            'fqt_id' => $this->fqt_id,
            'fqt_flight_quote_id' => $this->fqt_flight_quote_id,
            'fqt_duration' => $this->fqt_duration,
        ]);

        $query->andFilterWhere(['like', 'fqt_key', $this->fqt_key]);

        return $dataProvider;
    }
}
