<?php

namespace modules\product\src\entities\productQuoteObjectRefund\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund as ProductQuoteObjectRefundModel;

/**
 * ProductQuoteObjectRefund represents the model behind the search form of `modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund`.
 */
class ProductQuoteObjectRefund extends ProductQuoteObjectRefundModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqor_id', 'pqor_product_quote_refund_id', 'pqor_status_id', 'pqor_created_user_id', 'pqor_updated_user_id'], 'integer'],
            [['pqor_selling_price', 'pqor_penalty_amount', 'pqor_processing_fee_amount', 'pqor_refund_amount', 'pqor_client_currency_rate', 'pqor_client_selling_price', 'pqor_client_refund_amount'], 'number'],
            [['pqor_client_currency', 'pqor_created_dt', 'pqor_updated_dt'], 'safe'],
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
        $query = ProductQuoteObjectRefundModel::find();

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
            'pqor_id' => $this->pqor_id,
            'pqor_product_quote_refund_id' => $this->pqor_product_quote_refund_id,
            'pqor_selling_price' => $this->pqor_selling_price,
            'pqor_penalty_amount' => $this->pqor_penalty_amount,
            'pqor_processing_fee_amount' => $this->pqor_processing_fee_amount,
            'pqor_refund_amount' => $this->pqor_refund_amount,
            'pqor_status_id' => $this->pqor_status_id,
            'pqor_client_currency_rate' => $this->pqor_client_currency_rate,
            'pqor_client_selling_price' => $this->pqor_client_selling_price,
            'pqor_client_refund_amount' => $this->pqor_client_refund_amount,
            'pqor_created_user_id' => $this->pqor_created_user_id,
            'pqor_updated_user_id' => $this->pqor_updated_user_id,
            'date(pqor_created_dt)' => $this->pqor_created_dt,
            'date(pqor_updated_dt)' => $this->pqor_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pqor_client_currency', $this->pqor_client_currency]);

        return $dataProvider;
    }
}
