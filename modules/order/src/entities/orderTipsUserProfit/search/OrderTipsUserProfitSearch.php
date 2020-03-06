<?php

namespace modules\order\src\entities\orderTipsUserProfit\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;

/**
 * OrderTipsUserProfitSearch represents the model behind the search form of `modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit`.
 */
class OrderTipsUserProfitSearch extends OrderTipsUserProfit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['otup_order_id', 'otup_user_id', 'otup_percent', 'otup_created_user_id', 'otup_updated_user_id'], 'integer'],
            [['otup_amount'], 'number'],
            [['otup_created_dt', 'otup_updated_dt'], 'safe'],
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
        $query = OrderTipsUserProfit::find();

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
            'otup_order_id' => $this->otup_order_id,
            'otup_user_id' => $this->otup_user_id,
            'otup_percent' => $this->otup_percent,
            'otup_amount' => $this->otup_amount,
            'date_format(otup_created_dt, "%Y-%m-%d")' => $this->otup_created_dt,
            'date_format(otup_updated_dt, "%Y-%m-%d")' => $this->otup_updated_dt,
            'otup_created_user_id' => $this->otup_created_user_id,
            'otup_updated_user_id' => $this->otup_updated_user_id,
        ]);

        return $dataProvider;
    }
}
