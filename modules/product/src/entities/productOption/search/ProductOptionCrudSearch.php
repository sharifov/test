<?php

namespace modules\product\src\entities\productOption\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productOption\ProductOption;

/**
 * ProductOptionSearch represents the model behind the search form of `common\models\ProductOption`.
 */
class ProductOptionCrudSearch extends ProductOption
{
    public function rules(): array
    {
        return [
            [['po_id', 'po_product_type_id', 'po_price_type_id', 'po_enabled', 'po_created_user_id', 'po_updated_user_id'], 'integer'],
            [['po_key', 'po_name', 'po_description'], 'safe'],
            [['po_max_price', 'po_min_price', 'po_price'], 'number'],

            ['po_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['po_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = ProductOption::find()->with(['poCreatedUser', 'poUpdatedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->po_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'po_created_dt', $this->po_created_dt, $user->timezone);
        }

        if ($this->po_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'po_updated_dt', $this->po_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'po_id' => $this->po_id,
            'po_product_type_id' => $this->po_product_type_id,
            'po_price_type_id' => $this->po_price_type_id,
            'po_max_price' => $this->po_max_price,
            'po_min_price' => $this->po_min_price,
            'po_price' => $this->po_price,
            'po_enabled' => $this->po_enabled,
            'po_created_user_id' => $this->po_created_user_id,
            'po_updated_user_id' => $this->po_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'po_key', $this->po_key])
            ->andFilterWhere(['like', 'po_name', $this->po_name])
            ->andFilterWhere(['like', 'po_description', $this->po_description]);

        return $dataProvider;
    }
}
