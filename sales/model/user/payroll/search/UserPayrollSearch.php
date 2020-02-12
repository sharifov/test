<?php

namespace sales\model\user\payroll\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\payroll\UserPayroll;

/**
 * UserPayrollSearch represents the model behind the search form of `sales\model\user\payroll\UserPayroll`.
 */
class UserPayrollSearch extends UserPayroll
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ups_id', 'ups_user_id', 'ups_month', 'ups_year', 'ups_agent_status_id', 'ups_status_id'], 'integer'],
            [['ups_base_amount', 'ups_profit_amount', 'ups_tax_amount', 'ups_payment_amount', 'ups_total_amount'], 'number'],
            [['ups_created_dt', 'ups_updated_dt'], 'safe'],
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
        $query = UserPayroll::find();

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
            'ups_id' => $this->ups_id,
            'ups_user_id' => $this->ups_user_id,
            'ups_month' => $this->ups_month,
            'ups_year' => $this->ups_year,
            'ups_base_amount' => $this->ups_base_amount,
            'ups_profit_amount' => $this->ups_profit_amount,
            'ups_tax_amount' => $this->ups_tax_amount,
            'ups_payment_amount' => $this->ups_payment_amount,
            'ups_total_amount' => $this->ups_total_amount,
            'ups_agent_status_id' => $this->ups_agent_status_id,
            'ups_status_id' => $this->ups_status_id,
            'ups_created_dt' => $this->ups_created_dt,
            'ups_updated_dt' => $this->ups_updated_dt,
        ]);

        return $dataProvider;
    }
}
