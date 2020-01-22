<?php

namespace common\models\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    public function rules(): array
    {
        return [
            [['pr_id', 'pr_type_id', 'pr_lead_id', 'pr_status_id', 'pr_created_user_id', 'pr_updated_user_id'], 'integer'],
            [['pr_name', 'pr_description'], 'safe'],
            [['pr_service_fee_percent'], 'number'],

            ['pr_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['pr_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = Product::find()->with(['prUpdatedUser', 'prCreatedUser', 'prLead']);

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

        if ($this->pr_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pr_created_dt', $this->pr_created_dt, $user->timezone);
        }
        if ($this->pr_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pr_updated_dt', $this->pr_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pr_id' => $this->pr_id,
            'pr_type_id' => $this->pr_type_id,
            'pr_lead_id' => $this->pr_lead_id,
            'pr_status_id' => $this->pr_status_id,
            'pr_service_fee_percent' => $this->pr_service_fee_percent,
            'pr_created_user_id' => $this->pr_created_user_id,
            'pr_updated_user_id' => $this->pr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pr_name', $this->pr_name])
            ->andFilterWhere(['like', 'pr_description', $this->pr_description]);

        return $dataProvider;
    }
}
