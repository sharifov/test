<?php

namespace modules\product\src\entities\productType\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productType\ProductType;

/**
 * ProductTypeSearch represents the model behind the search form of `common\models\ProductType`.
 */
class ProductTypeCrudSearch extends ProductType
{
    public function rules(): array
    {
        return [
            [['pt_id', 'pt_enabled'], 'integer'],
            [['pt_service_fee_percent'], 'number'],
            [['pt_key', 'pt_name', 'pt_description', 'pt_settings'], 'safe'],

            ['pt_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['pt_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = ProductType::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['pt_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pt_id' => $this->pt_id,
            'pt_enabled' => $this->pt_enabled,
            'pt_service_fee_percent' => $this->pt_service_fee_percent,
        ]);

        if ($this->pt_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pt_created_dt', $this->pt_created_dt, $user->timezone);
        }

        if ($this->pt_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pt_updated_dt', $this->pt_updated_dt, $user->timezone);
        }

        $query->andFilterWhere(['like', 'pt_key', $this->pt_key])
            ->andFilterWhere(['like', 'pt_name', $this->pt_name])
            ->andFilterWhere(['like', 'pt_description', $this->pt_description])
            ->andFilterWhere(['like', 'pt_settings', $this->pt_settings]);

        return $dataProvider;
    }
}
