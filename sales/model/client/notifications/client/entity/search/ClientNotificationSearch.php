<?php

namespace sales\model\client\notifications\client\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\client\notifications\client\entity\ClientNotification;

class ClientNotificationSearch extends ClientNotification
{
    public function rules(): array
    {
        return [
            ['cn_client_id', 'integer'],

            ['cn_communication_object_id', 'integer'],

            ['cn_communication_type_id', 'integer'],

            ['cn_id', 'integer'],

            ['cn_notification_type_id', 'integer'],

            ['cn_object_id', 'integer'],

            ['cn_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cn_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cn_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cn_created_dt', $this->cn_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cn_id' => $this->cn_id,
            'cn_client_id' => $this->cn_client_id,
            'cn_notification_type_id' => $this->cn_notification_type_id,
            'cn_object_id' => $this->cn_object_id,
            'cn_communication_type_id' => $this->cn_communication_type_id,
            'cn_communication_object_id' => $this->cn_communication_object_id,
        ]);

        return $dataProvider;
    }
}
