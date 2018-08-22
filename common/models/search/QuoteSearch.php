<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Quote;

/**
 * QuoteSearch represents the model behind the search form of `common\models\Quote`.
 */
class QuoteSearch extends Quote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lead_id', 'employee_id', 'status', 'check_payment'], 'integer'],
            [['uid', 'record_locator', 'pcc', 'cabin', 'gds', 'trip_type', 'main_airline_code', 'reservation_dump', 'fare_type', 'created', 'updated'], 'safe'],
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
        $query = Quote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
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
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'check_payment' => $this->check_payment,
            'created' => $this->created,
            'updated' => $this->updated,
        ]);

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'record_locator', $this->record_locator])
            ->andFilterWhere(['like', 'pcc', $this->pcc])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'gds', $this->gds])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'main_airline_code', $this->main_airline_code])
            ->andFilterWhere(['like', 'reservation_dump', $this->reservation_dump])
            ->andFilterWhere(['like', 'fare_type', $this->fare_type]);

        return $dataProvider;
    }
}
