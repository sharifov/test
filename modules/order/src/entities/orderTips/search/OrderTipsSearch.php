<?php

namespace modules\order\src\entities\orderTips\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderTips\OrderTips;

/**
 * OrderTipsSearch represents the model behind the search form of `modules\order\src\entities\orderTips\OrderTips`.
 */
class OrderTipsSearch extends OrderTips
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ot_id', 'ot_order_id', 'ot_user_profit_percent'], 'integer'],
            [['ot_client_amount', 'ot_amount', 'ot_user_profit'], 'number'],
            [['ot_description', 'ot_created_dt', 'ot_updated_dt'], 'safe'],
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
        $query = OrderTips::find();

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
            'ot_id' => $this->ot_id,
            'ot_order_id' => $this->ot_order_id,
            'ot_client_amount' => $this->ot_client_amount,
            'ot_amount' => $this->ot_amount,
			'ot_user_profit_percent' => $this->ot_user_profit_percent,
			'ot_user_profit' => $this->ot_user_profit,
            'date_format(ot_created_dt, "%Y-%m-%d")' => $this->ot_created_dt,
            'date_format(ot_updated_dt, "%Y-%m-%d")' => $this->ot_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ot_description', $this->ot_description]);

        return $dataProvider;
    }
}
