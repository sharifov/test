<?php

namespace src\model\client\notifications\email\entity\search;

use common\models\Employee;
use src\model\client\notifications\email\entity\ClientNotificationEmailList;
use yii\data\ActiveDataProvider;

class ClientNotificationEmailListSearch extends ClientNotificationEmailList
{
    public function rules(): array
    {
        return [
            ['cnel_email_id', 'string'],

            ['cnel_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cnel_end', 'date', 'format' => 'php:Y-m-d'],

            ['cnel_from_email_id', 'integer'],

            ['cnel_name_from', 'string'],

            ['cnel_id', 'integer'],

            ['cnel_start', 'date', 'format' => 'php:Y-m-d'],

            ['cnel_status_id', 'integer'],

            ['cnel_to_client_email_id', 'integer'],

            ['cnel_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cnel_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cnel_start) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnel_start', $this->cnel_start, $user->timezone);
        }

        if ($this->cnel_end) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnel_end', $this->cnel_end, $user->timezone);
        }

        if ($this->cnel_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnel_created_dt', $this->cnel_created_dt, $user->timezone);
        }

        if ($this->cnel_updated_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cnel_updated_dt', $this->cnel_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cnel_id' => $this->cnel_id,
            'cnel_email_id' => $this->cnel_email_id,
            'cnel_status_id' => $this->cnel_status_id,
            'cnel_from_email_id' => $this->cnel_from_email_id,
            'cnel_to_client_email_id' => $this->cnel_to_client_email_id,
        ]);

        return $dataProvider;
    }
}
