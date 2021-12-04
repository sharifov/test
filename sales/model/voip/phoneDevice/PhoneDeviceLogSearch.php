<?php

namespace sales\model\voip\phoneDevice;

use yii\data\ActiveDataProvider;
use sales\model\voip\phoneDevice\PhoneDeviceLog;

class PhoneDeviceLogSearch extends PhoneDeviceLog
{
    public function rules(): array
    {
        return [
            ['pdl_created_dt', 'safe'],

            ['pdl_device_id', 'integer'],

            ['pdl_error', 'safe'],

            ['pdl_id', 'integer'],

            ['pdl_level', 'integer'],

            ['pdl_message', 'safe'],

            ['pdl_timestamp_ts', 'integer'],

            ['pdl_user_id', 'integer'],
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
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pdl_id' => $this->pdl_id,
            'pdl_user_id' => $this->pdl_user_id,
            'pdl_device_id' => $this->pdl_device_id,
            'pdl_level' => $this->pdl_level,
            'pdl_timestamp_ts' => $this->pdl_timestamp_ts,
            'pdl_created_dt' => $this->pdl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'pdl_message', $this->pdl_message])
            ->andFilterWhere(['like', 'pdl_error', $this->pdl_error]);

        return $dataProvider;
    }
}
