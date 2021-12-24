<?php

namespace sales\model\smsSubscribe\entity;

use yii\data\ActiveDataProvider;
use sales\model\smsSubscribe\entity\SmsSubscribe;

class SmsSubscribeSearch extends SmsSubscribe
{
    public function rules(): array
    {
        return [
            ['ss_cpl_id', 'integer'],
            ['ss_created_user_id', 'integer'],
            ['ss_id', 'integer'],
            ['ss_project_id', 'integer'],
            ['ss_status_id', 'integer'],
            ['ss_updated_user_id', 'integer'],

            [['ss_created_dt', 'ss_updated_dt', 'ss_deadline_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ss_id' => $this->ss_id,
            'ss_cpl_id' => $this->ss_cpl_id,
            'ss_project_id' => $this->ss_project_id,
            'ss_status_id' => $this->ss_status_id,
            'DATE(ss_created_dt)' => $this->ss_created_dt,
            'DATE(ss_updated_dt)' => $this->ss_updated_dt,
            'DATE(ss_deadline_dt)' => $this->ss_deadline_dt,
            'ss_created_user_id' => $this->ss_created_user_id,
            'ss_updated_user_id' => $this->ss_updated_user_id,
        ]);

        return $dataProvider;
    }
}
