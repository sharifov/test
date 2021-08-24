<?php

namespace modules\order\src\entities\orderRefund\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderRefund\OrderRefund;

/**
 * OrderRefundSearch represents the model behind the search form of `modules\order\src\entities\orderRefund\OrderRefund`.
 */
class OrderRefundSearch extends OrderRefund
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['orr_id', 'orr_order_id', 'orr_client_status_id', 'orr_status_id', 'orr_created_user_id', 'orr_updated_user_id'], 'integer'],
            [['orr_uid', 'orr_client_currency', 'orr_description', 'orr_expiration_dt', 'orr_created_dt', 'orr_updated_dt'], 'safe'],
            [['orr_selling_price', 'orr_penalty_amount', 'orr_processing_fee_amount', 'orr_charge_amount', 'orr_refund_amount', 'orr_client_currency_rate', 'orr_client_selling_price', 'orr_client_charge_amount', 'orr_client_refund_amount'], 'number'],
            ['orr_case_id', 'integer'],
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
        $query = OrderRefund::find();

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
            'orr_id' => $this->orr_id,
            'orr_order_id' => $this->orr_order_id,
            'orr_case_id' => $this->orr_case_id,
            'orr_selling_price' => $this->orr_selling_price,
            'orr_penalty_amount' => $this->orr_penalty_amount,
            'orr_processing_fee_amount' => $this->orr_processing_fee_amount,
            'orr_charge_amount' => $this->orr_charge_amount,
            'orr_refund_amount' => $this->orr_refund_amount,
            'orr_client_status_id' => $this->orr_client_status_id,
            'orr_status_id' => $this->orr_status_id,
            'orr_client_currency_rate' => $this->orr_client_currency_rate,
            'orr_client_selling_price' => $this->orr_client_selling_price,
            'orr_client_charge_amount' => $this->orr_client_charge_amount,
            'orr_client_refund_amount' => $this->orr_client_refund_amount,
            'orr_expiration_dt' => $this->orr_expiration_dt,
            'orr_created_user_id' => $this->orr_created_user_id,
            'orr_updated_user_id' => $this->orr_updated_user_id,
            'date(orr_created_dt)' => $this->orr_created_dt,
            'date(orr_updated_dt)' => $this->orr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'orr_uid', $this->orr_uid])
            ->andFilterWhere(['like', 'orr_client_currency', $this->orr_client_currency])
            ->andFilterWhere(['like', 'orr_description', $this->orr_description]);

        return $dataProvider;
    }
}
