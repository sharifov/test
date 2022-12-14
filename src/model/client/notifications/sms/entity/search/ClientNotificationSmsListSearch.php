<?php

namespace src\model\client\notifications\sms\entity\search;

use common\models\Employee;
use src\model\client\notifications\sms\entity\ClientNotificationSmsList;
use yii\data\ActiveDataProvider;

class ClientNotificationSmsListSearch extends ClientNotificationSmsList
{
    public function rules(): array
    {
        return [
            ['cnsl_sms_id', 'string'],

            ['cnsl_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cnsl_end', 'date', 'format' => 'php:Y-m-d'],

            ['cnsl_from_phone_id', 'integer'],

            ['cnsl_name_from', 'string'],

            ['cnsl_id', 'integer'],

            ['cnsl_start', 'date', 'format' => 'php:Y-m-d'],

            ['cnsl_status_id', 'integer'],

            ['cnsl_to_client_phone_id', 'integer'],

            ['cnsl_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cnsl_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cnsl_start) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnsl_start', $this->cnsl_start, $user->timezone);
        }

        if ($this->cnsl_end) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnsl_end', $this->cnsl_end, $user->timezone);
        }

        if ($this->cnsl_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnsl_created_dt', $this->cnsl_created_dt, $user->timezone);
        }

        if ($this->cnsl_updated_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnsl_updated_dt', $this->cnsl_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cnsl_id' => $this->cnsl_id,
            'cnsl_sms_id' => $this->cnsl_sms_id,
            'cnsl_status_id' => $this->cnsl_status_id,
            'cnsl_from_phone_id' => $this->cnsl_from_phone_id,
            'cnsl_to_client_phone_id' => $this->cnsl_to_client_phone_id,
        ]);

        return $dataProvider;
    }
}
