<?php

namespace modules\product\src\entities\productQuoteOption\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;

/**
 * ProductQuoteOptionSearch represents the model behind the search form of `common\models\ProductQuoteOption`.
 */
class ProductQuoteOptionCrudSearch extends ProductQuoteOption
{
    public function rules(): array
    {
        return [
            [['pqo_id', 'pqo_product_quote_id', 'pqo_product_option_id', 'pqo_status_id', 'pqo_created_user_id', 'pqo_updated_user_id'], 'integer'],
            [['pqo_name', 'pqo_description'], 'safe'],
            [['pqo_price', 'pqo_client_price', 'pqo_extra_markup'], 'number'],

            ['pqo_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['pqo_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = ProductQuoteOption::find();

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

        if ($this->pqo_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pqo_created_dt', $this->pqo_created_dt, $user->timezone);
        }

        if ($this->pqo_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pqo_updated_dt', $this->pqo_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pqo_id' => $this->pqo_id,
            'pqo_product_quote_id' => $this->pqo_product_quote_id,
            'pqo_product_option_id' => $this->pqo_product_option_id,
            'pqo_status_id' => $this->pqo_status_id,
            'pqo_price' => $this->pqo_price,
            'pqo_client_price' => $this->pqo_client_price,
            'pqo_extra_markup' => $this->pqo_extra_markup,
            'pqo_created_user_id' => $this->pqo_created_user_id,
            'pqo_updated_user_id' => $this->pqo_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pqo_name', $this->pqo_name])
            ->andFilterWhere(['like', 'pqo_description', $this->pqo_description]);

        return $dataProvider;
    }
}
