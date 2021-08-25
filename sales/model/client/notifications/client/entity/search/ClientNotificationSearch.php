<?php

namespace sales\model\client\notifications\client\entity\search;

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
