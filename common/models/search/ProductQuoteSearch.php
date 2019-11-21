<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductQuote;

/**
 * ProductQuoteSearch represents the model behind the search form of `common\models\ProductQuote`.
 */
class ProductQuoteSearch extends ProductQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pq_id', 'pq_product_id', 'pq_order_id', 'pq_status_id', 'pq_owner_user_id', 'pq_created_user_id', 'pq_updated_user_id'], 'integer'],
            [['pq_gid', 'pr_name', 'pq_description', 'pq_origin_currency', 'pq_client_currency', 'pq_created_dt', 'pq_updated_dt'], 'safe'],
            [['pq_price', 'pq_origin_price', 'pq_client_price', 'pq_service_fee_sum', 'pq_origin_currency_rate', 'pq_client_currency_rate'], 'number'],
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
        $query = ProductQuote::find();

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
            'pq_id' => $this->pq_id,
            'pq_product_id' => $this->pq_product_id,
            'pq_order_id' => $this->pq_order_id,
            'pq_status_id' => $this->pq_status_id,
            'pq_price' => $this->pq_price,
            'pq_origin_price' => $this->pq_origin_price,
            'pq_client_price' => $this->pq_client_price,
            'pq_service_fee_sum' => $this->pq_service_fee_sum,
            'pq_origin_currency_rate' => $this->pq_origin_currency_rate,
            'pq_client_currency_rate' => $this->pq_client_currency_rate,
            'pq_owner_user_id' => $this->pq_owner_user_id,
            'pq_created_user_id' => $this->pq_created_user_id,
            'pq_updated_user_id' => $this->pq_updated_user_id,
            'pq_created_dt' => $this->pq_created_dt,
            'pq_updated_dt' => $this->pq_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pq_gid', $this->pq_gid])
            ->andFilterWhere(['like', 'pr_name', $this->pr_name])
            ->andFilterWhere(['like', 'pq_description', $this->pq_description])
            ->andFilterWhere(['like', 'pq_origin_currency', $this->pq_origin_currency])
            ->andFilterWhere(['like', 'pq_client_currency', $this->pq_client_currency]);

        return $dataProvider;
    }
}
