<?php

namespace sales\model\client\notifications\phone\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneList;

class ClientNotificationPhoneListSearch extends ClientNotificationPhoneList
{
    public function rules(): array
    {
        return [
            ['cnfl_call_sid', 'string'],

            ['cnfl_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cnfl_end', 'date', 'format' => 'php:Y-m-d'],

            ['cnfl_file_url', 'string'],

            ['cnfl_from_phone_id', 'integer'],

            ['cnfl_id', 'integer'],

            ['cnfl_message', 'string'],

            ['cnfl_start', 'date', 'format' => 'php:Y-m-d'],

            ['cnfl_status_id', 'integer'],

            ['cnfl_to_client_phone_id', 'integer'],

            ['cnfl_updated_dt', 'date', 'format' => 'php:Y-m-d'],
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

        if ($this->cnfl_start) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnfl_start', $this->cnfl_start, $user->timezone);
        }

        if ($this->cnfl_end) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnfl_end', $this->cnfl_end, $user->timezone);
        }

        if ($this->cnfl_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnfl_created_dt', $this->cnfl_created_dt, $user->timezone);
        }

        if ($this->cnfl_updated_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnfl_updated_dt', $this->cnfl_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cnfl_id' => $this->cnfl_id,
            'cnfl_status_id' => $this->cnfl_status_id,
            'cnfl_from_phone_id' => $this->cnfl_from_phone_id,
            'cnfl_to_client_phone_id' => $this->cnfl_to_client_phone_id,
        ]);

        $query->andFilterWhere(['like', 'cnfl_message', $this->cnfl_message])
            ->andFilterWhere(['like', 'cnfl_file_url', $this->cnfl_file_url])
            ->andFilterWhere(['like', 'cnfl_call_sid', $this->cnfl_call_sid]);

        return $dataProvider;
    }
}
