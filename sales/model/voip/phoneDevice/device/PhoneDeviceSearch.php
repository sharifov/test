<?php

namespace sales\model\voip\phoneDevice\device;

use common\models\Employee;
use yii\data\ActiveDataProvider;

class PhoneDeviceSearch extends PhoneDevice
{
    public function rules(): array
    {
        return [
            ['pd_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['pd_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['pd_device_identity', 'string'],

            ['pd_connection_id', 'integer'],

            ['pd_id', 'integer'],

            ['pd_ip_address', 'string'],

            ['pd_user_agent', 'string'],

            ['pd_name', 'string'],

            ['pd_status_device', 'boolean'],

            ['pd_status_microphone', 'boolean'],

            ['pd_status_speaker', 'boolean'],

            ['pd_user_id', 'integer'],

            ['pd_buid', 'string'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pd_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->pd_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'pd_created_dt', $this->pd_created_dt, $user->timezone);
        }

        if ($this->pd_updated_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'pd_updated_dt', $this->pd_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'pd_id' => $this->pd_id,
            'pd_user_id' => $this->pd_user_id,
            'pd_connection_id' => $this->pd_connection_id,
            'pd_status_device' => $this->pd_status_device,
            'pd_status_speaker' => $this->pd_status_speaker,
            'pd_status_microphone' => $this->pd_status_microphone,
            'pd_buid' => $this->pd_buid,
        ]);

        $query->andFilterWhere(['like', 'pd_name', $this->pd_name])
            ->andFilterWhere(['like', 'pd_device_identity', $this->pd_device_identity])
            ->andFilterWhere(['like', 'pd_ip_address', $this->pd_ip_address])
            ->andFilterWhere(['like', 'pd_user_agent', $this->pd_user_agent]);

        return $dataProvider;
    }
}
