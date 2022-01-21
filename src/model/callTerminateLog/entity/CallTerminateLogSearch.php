<?php

namespace src\model\callTerminateLog\entity;

use yii\data\ActiveDataProvider;
use src\model\callTerminateLog\entity\CallTerminateLog;

class CallTerminateLogSearch extends CallTerminateLog
{
    public function rules(): array
    {
        return [
            ['ctl_call_phone_number', 'safe'],

            ['ctl_call_status_id', 'integer'],

            ['ctl_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ctl_id', 'integer'],

            ['ctl_project_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ctl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ctl_id' => $this->ctl_id,
            'ctl_call_status_id' => $this->ctl_call_status_id,
            'ctl_project_id' => $this->ctl_project_id,
            'DATE(ctl_created_dt)' => $this->ctl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ctl_call_phone_number', $this->ctl_call_phone_number]);

        return $dataProvider;
    }
}
