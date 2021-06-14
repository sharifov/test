<?php

namespace sales\model\clientChat\componentEvent\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;

class ClientChatComponentEventSearch extends ClientChatComponentEvent
{
    public function rules(): array
    {
        return [
            ['ccce_chat_channel_id', 'integer'],

            ['ccce_component', 'integer'],

            ['ccce_component_config', 'safe'],

            ['ccce_created_dt', 'safe'],

            ['ccce_created_user_id', 'integer'],

            ['ccce_enabled', 'integer'],

            ['ccce_event_type', 'integer'],

            ['ccce_id', 'integer'],

            ['ccce_sort_order', 'integer'],

            ['ccce_updated_dt', 'safe'],

            ['ccce_updated_user_id', 'integer'],
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
            'ccce_id' => $this->ccce_id,
            'ccce_chat_channel_id' => $this->ccce_chat_channel_id,
            'ccce_component' => $this->ccce_component,
            'ccce_event_type' => $this->ccce_event_type,
            'ccce_enabled' => $this->ccce_enabled,
            'ccce_sort_order' => $this->ccce_sort_order,
            'ccce_created_user_id' => $this->ccce_created_user_id,
            'ccce_updated_user_id' => $this->ccce_updated_user_id,
            'ccce_created_dt' => $this->ccce_created_dt,
            'ccce_updated_dt' => $this->ccce_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ccce_component_config', $this->ccce_component_config]);

        return $dataProvider;
    }
}
