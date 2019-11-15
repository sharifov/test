<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Currency;

/**
 * CurrencySearch represents the model behind the search form of `common\models\Currency`.
 */
class CurrencySearch extends Currency
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cur_code', 'cur_name', 'cur_symbol', 'cur_created_dt', 'cur_updated_dt', 'cur_synch_dt'], 'safe'],
            [['cur_base_rate', 'cur_app_rate', 'cur_app_percent'], 'number'],
            [['cur_enabled', 'cur_default', 'cur_sort_order'], 'integer'],
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
        $query = Currency::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cur_sort_order' => SORT_DESC]],
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
            'cur_base_rate' => $this->cur_base_rate,
            'cur_app_rate' => $this->cur_app_rate,
            'cur_app_percent' => $this->cur_app_percent,
            'cur_enabled' => $this->cur_enabled,
            'cur_default' => $this->cur_default,
            'cur_sort_order' => $this->cur_sort_order,
            'cur_created_dt' => $this->cur_created_dt,
            'cur_updated_dt' => $this->cur_updated_dt,
            'cur_synch_dt' => $this->cur_synch_dt,
        ]);

        $query->andFilterWhere(['like', 'cur_code', $this->cur_code])
            ->andFilterWhere(['like', 'cur_name', $this->cur_name])
            ->andFilterWhere(['like', 'cur_symbol', $this->cur_symbol]);

        return $dataProvider;
    }
}
