<?php

namespace modules\order\src\entities\orderProduct\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderProduct\OrderProduct;

class OrderProductCrudSearch extends OrderProduct
{
    public function rules(): array
    {
        return [
            [['orp_order_id', 'orp_product_quote_id', 'orp_created_user_id'], 'integer'],

            ['orp_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = OrderProduct::find()->with(['orpCreatedUser', 'orpOrder', 'orpProductQuote']);

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

        if ($this->orp_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'orp_created_dt', $this->orp_created_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'orp_order_id' => $this->orp_order_id,
            'orp_product_quote_id' => $this->orp_product_quote_id,
            'orp_created_user_id' => $this->orp_created_user_id,
        ]);

        return $dataProvider;
    }
}
