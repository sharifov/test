<?php

namespace modules\product\src\entities\product\search;

use common\models\Employee;
use common\models\Lead;
use modules\product\src\entities\productType\ProductType;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\product\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductCrudSearch extends Product
{
    public function rules(): array
    {
        return [
            ['pr_id', 'integer'],
            ['pr_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['pr_id' => 'pr_id']],

            ['pr_type_id', 'integer'],
            ['pr_type_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['pr_type_id' => 'pt_id']],

            ['pr_lead_id', 'integer'],
            ['pr_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pr_lead_id' => 'id']],

            ['pr_status_id', 'integer'],

            ['pr_created_user_id', 'integer'],
            ['pr_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_created_user_id' => 'id']],

            ['pr_updated_user_id', 'integer'],
            ['pr_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_updated_user_id' => 'id']],

            ['pr_description', 'string'],

            ['pr_service_fee_percent', 'number'],

            ['pr_market_price', 'number'],

            ['pr_client_budget', 'number'],

            ['pr_name', 'string', 'max' => 40],

            [['pr_created_dt', 'pr_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['pr_project_id', 'integer'],

            ['pr_gid', 'string', 'max' => 32],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['prUpdatedUser', 'prCreatedUser', 'prLead']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['pr_id' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
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
            'pr_market_price' => $this->pr_market_price,
            'pr_client_budget' => $this->pr_client_budget,
            'pr_created_user_id' => $this->pr_created_user_id,
            'pr_updated_user_id' => $this->pr_updated_user_id,
            'pr_project_id' => $this->pr_project_id,
        ]);

        $query->andFilterWhere(['like', 'pr_name', $this->pr_name])
            ->andFilterWhere(['like', 'pr_gid', $this->pr_gid])
            ->andFilterWhere(['like', 'pr_description', $this->pr_description]);

        return $dataProvider;
    }
}
