<?php

namespace sales\model\user\profit\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\profit\UserProfit;

/**
 * UserProfitSearch represents the model behind the search form of `sales\model\user\profit\UserProfit`.
 */
class UserProfitSearch extends UserProfit
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['up_id', 'up_user_id', 'up_lead_id', 'up_order_id', 'up_product_quote_id', 'up_percent', 'up_status_id', 'up_payroll_id', 'up_type_id'], 'integer'],
            [['up_profit', 'up_split_percent', 'up_amount'], 'number'],
            [['up_created_dt', 'up_updated_dt'], 'safe'],
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
        $query = UserProfit::find();

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
            'up_id' => $this->up_id,
            'up_user_id' => $this->up_user_id,
            'up_lead_id' => $this->up_lead_id,
            'up_order_id' => $this->up_order_id,
            'up_product_quote_id' => $this->up_product_quote_id,
            'up_percent' => $this->up_percent,
            'up_profit' => $this->up_profit,
            'up_split_percent' => $this->up_split_percent,
            'up_amount' => $this->up_amount,
            'up_status_id' => $this->up_status_id,
            'up_created_dt' => $this->up_created_dt,
            'up_updated_dt' => $this->up_updated_dt,
            'up_payroll_id' => $this->up_payroll_id,
            'up_type_id' => $this->up_type_id,
        ]);

        return $dataProvider;
    }
}
