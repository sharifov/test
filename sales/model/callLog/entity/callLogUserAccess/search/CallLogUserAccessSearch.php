<?php

namespace sales\model\callLog\entity\callLogUserAccess\search;

use common\models\CallUserAccess;
use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess;

class CallLogUserAccessSearch extends CallLogUserAccess
{
    public function rules(): array
    {
        return [
            ['clua_id', 'integer'],

            ['clua_access_finish_dt', 'date', 'format' => 'php:Y-m-d'],

            ['clua_access_start_dt', 'date', 'format' => 'php:Y-m-d'],

            ['clua_access_status_id', 'in', 'range' => array_keys(CallUserAccess::STATUS_TYPE_LIST)],

            ['clua_cl_id', 'integer'],

            ['clua_user_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()->with(['user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['clua_id' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->clua_access_start_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'clua_access_start_dt', $this->clua_access_start_dt, $user->timezone);
        }
        if ($this->clua_access_finish_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'clua_access_finish_dt', $this->clua_access_finish_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'clua_id' => $this->clua_id,
            'clua_cl_id' => $this->clua_cl_id,
            'clua_user_id' => $this->clua_user_id,
            'clua_access_status_id' => $this->clua_access_status_id,
        ]);

        return $dataProvider;
    }
}
