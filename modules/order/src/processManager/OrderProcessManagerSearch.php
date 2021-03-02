<?php

namespace modules\order\src\processManager;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class OrderProcessManagerSearch extends OrderProcessManager
{
    public function rules(): array
    {
        return [
            ['opm_status', 'integer'],

            ['opm_id', 'integer'],

            ['opm_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'opm_id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->opm_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'opm_created_dt', $this->opm_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'opm_id' => $this->opm_id,
            'opm_status' => $this->opm_status,
        ]);

        return $dataProvider;
    }
}
