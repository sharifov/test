<?php

namespace modules\order\src\entities\orderUserProfit\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;

/**
 * OrderUserProfitSearch represents the model behind the search form of `modules\order\src\entities\orderUserProfit\OrderUserProfit`.
 */
class OrderUserProfitSearch extends OrderUserProfit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['oup_order_id', 'oup_user_id', 'oup_percent', 'oup_created_user_id', 'oup_updated_user_id'], 'integer'],
            [['oup_amount'], 'number'],
            [['oup_created_dt', 'oup_updated_dt'], 'safe'],
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
        $query = OrderUserProfit::find();

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
            'oup_order_id' => $this->oup_order_id,
            'oup_user_id' => $this->oup_user_id,
            'oup_percent' => $this->oup_percent,
            'oup_amount' => $this->oup_amount,
            'oup_created_dt' => $this->oup_created_dt,
            'oup_updated_dt' => $this->oup_updated_dt,
            'oup_created_user_id' => $this->oup_created_user_id,
            'oup_updated_user_id' => $this->oup_updated_user_id,
        ]);

        return $dataProvider;
    }
}
