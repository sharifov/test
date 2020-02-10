<?php

namespace modules\product\src\entities\productQuote\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * ProductQuoteSearch represents the model behind the search form of `common\models\ProductQuote`.
 */
class ProductQuoteCrudSearch extends ProductQuote
{
    public function rules(): array
    {
        return [
            [['pq_id', 'pq_product_id', 'pq_order_id', 'pq_status_id', 'pq_owner_user_id', 'pq_created_user_id', 'pq_updated_user_id'], 'integer'],
            [['pq_gid', 'pq_name', 'pq_description', 'pq_origin_currency', 'pq_client_currency'], 'safe'],
            [['pq_price', 'pq_origin_price', 'pq_client_price', 'pq_service_fee_sum', 'pq_origin_currency_rate', 'pq_client_currency_rate'], 'number'],

            ['pq_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['pq_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['pq_clone_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = ProductQuote::find()->with(['pqProduct', 'pqOwnerUser', 'pqCreatedUser', 'pqUpdatedUser']);

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

        if ($this->pq_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pq_created_dt', $this->pq_created_dt, $user->timezone);
        }

        if ($this->pq_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pq_updated_dt', $this->pq_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pq_id' => $this->pq_id,
            'pq_clone_id' => $this->pq_clone_id,
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
        ]);

        $query->andFilterWhere(['like', 'pq_gid', $this->pq_gid])
            ->andFilterWhere(['like', 'pq_name', $this->pq_name])
            ->andFilterWhere(['like', 'pq_description', $this->pq_description])
            ->andFilterWhere(['like', 'pq_origin_currency', $this->pq_origin_currency])
            ->andFilterWhere(['like', 'pq_client_currency', $this->pq_client_currency]);

        return $dataProvider;
    }
}
