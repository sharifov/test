<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuotePrice;

/**
 * QuotePriceSearch represents the model behind the search form of `common\models\QuotePrice`.
 */
class QuotePriceSearch extends QuotePrice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'quote_id'], 'integer'],
            [['passenger_type', 'created', 'updated'], 'safe'],
            [['selling', 'net', 'fare', 'taxes', 'mark_up', 'extra_mark_up'], 'number'],
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
        $query = QuotePrice::find()->with('quote');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
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
            'quote_id' => $this->quote_id,
            'selling' => $this->selling,
            'net' => $this->net,
            'fare' => $this->fare,
            'taxes' => $this->taxes,
            'mark_up' => $this->mark_up,
            'extra_mark_up' => $this->extra_mark_up,
            'created' => $this->created,
            'updated' => $this->updated,
        ]);

        $query->andFilterWhere(['like', 'passenger_type', $this->passenger_type]);

        return $dataProvider;
    }
}
