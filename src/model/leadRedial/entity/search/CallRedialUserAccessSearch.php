<?php

namespace src\model\leadRedial\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use src\model\leadRedial\entity\CallRedialUserAccess;

class CallRedialUserAccessSearch extends CallRedialUserAccess
{
    public function rules(): array
    {
        return [
            ['crua_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['crua_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['crua_lead_id', 'integer'],

            ['crua_user_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->crua_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'crua_created_dt', $this->crua_created_dt, $user->timezone);
        }

        if ($this->crua_updated_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'crua_updated_dt', $this->crua_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'crua_lead_id' => $this->crua_lead_id,
            'crua_user_id' => $this->crua_user_id,
            'crua_updated_dt' => $this->crua_updated_dt,
        ]);

        return $dataProvider;
    }
}
