<?php

namespace modules\product\src\entities\productQuoteRefund\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;

/**
 * ProductQuoteRefundSearch represents the model behind the search form of `modules\product\src\entities\productQuoteRefund\ProductQuoteRefund`.
 */
class ProductQuoteRefundSearch extends ProductQuoteRefund
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqr_id', 'pqr_order_refund_id', 'pqr_status_id', 'pqr_created_user_id', 'pqr_updated_user_id', 'pqr_type_id'], 'integer'],
            [['pqr_selling_price', 'pqr_penalty_amount', 'pqr_processing_fee_amount', 'pqr_refund_amount', 'pqr_client_currency_rate', 'pqr_client_selling_price', 'pqr_client_refund_amount'], 'number'],
            [['pqr_client_currency', 'pqr_created_dt', 'pqr_updated_dt'], 'safe'],
            ['pqr_case_id', 'integer'],
            [['pqr_gid', 'pqr_cid'], 'string', 'max' => 32]
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
        $query = ProductQuoteRefund::find();

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
            'pqr_id' => $this->pqr_id,
            'pqr_case_id' => $this->pqr_case_id,
            'pqr_order_refund_id' => $this->pqr_order_refund_id,
            'pqr_selling_price' => $this->pqr_selling_price,
            'pqr_penalty_amount' => $this->pqr_penalty_amount,
            'pqr_processing_fee_amount' => $this->pqr_processing_fee_amount,
            'pqr_refund_amount' => $this->pqr_refund_amount,
            'pqr_status_id' => $this->pqr_status_id,
            'pqr_client_currency_rate' => $this->pqr_client_currency_rate,
            'pqr_client_selling_price' => $this->pqr_client_selling_price,
            'pqr_client_refund_amount' => $this->pqr_client_refund_amount,
            'pqr_created_user_id' => $this->pqr_created_user_id,
            'pqr_updated_user_id' => $this->pqr_updated_user_id,
            'date(pqr_created_dt)' => $this->pqr_created_dt,
            'date(pqr_updated_dt)' => $this->pqr_updated_dt,
            'pqr_type_id' => $this->pqr_type_id,
        ]);

        $query->andFilterWhere(['like', 'pqr_client_currency', $this->pqr_client_currency]);
        $query->andFilterWhere(['like', 'pqr_gid', $this->pqr_gid]);
        $query->andFilterWhere(['like', 'pqr_cid', $this->pqr_cid]);

        return $dataProvider;
    }
}
