<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteStatusLog;

/**
 * FlightQuoteStatusLogSearch represents the model behind the search form of `modules\flight\models\FlightQuoteStatusLog`.
 */
class FlightQuoteStatusLogSearch extends FlightQuoteStatusLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsl_id', 'qsl_created_user_id', 'qsl_flight_quote_id', 'qsl_status_id'], 'integer'],
            [['qsl_created_dt'], 'safe'],
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
        $query = FlightQuoteStatusLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['qsl_id' => SORT_DESC]],
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
            'qsl_id' => $this->qsl_id,
            'qsl_created_user_id' => $this->qsl_created_user_id,
            'qsl_flight_quote_id' => $this->qsl_flight_quote_id,
            'qsl_status_id' => $this->qsl_status_id,
            'qsl_created_dt' => $this->qsl_created_dt,
        ]);

        return $dataProvider;
    }
}
