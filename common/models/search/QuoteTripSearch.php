<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteTrip;

/**
 * QuoteTripSearch represents the model behind the search form of `common\models\QuoteTrip`.
 */
class QuoteTripSearch extends QuoteTrip
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qt_id', 'qt_duration', 'qt_quote_id'], 'integer'],
            [['qt_key'], 'safe'],
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
        $query = QuoteTrip::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['qt_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qt_id' => $this->qt_id,
            'qt_duration' => $this->qt_duration,
            'qt_quote_id' => $this->qt_quote_id,
        ]);

        $query->andFilterWhere(['like', 'qt_key', $this->qt_key]);

        return $dataProvider;
    }
}
