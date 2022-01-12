<?php

namespace src\model\clientChat\componentRule\entity\search;

use yii\data\ActiveDataProvider;
use src\model\clientChat\componentRule\entity\ClientChatComponentRule;

class ClientChatComponentRuleSearch extends ClientChatComponentRule
{
    public function rules(): array
    {
        return [
            ['cccr_component_config', 'safe'],

            ['cccr_component_event_id', 'integer'],

            ['cccr_created_dt', 'safe'],

            ['cccr_created_user_id', 'integer'],

            ['cccr_enabled', 'integer'],

            ['cccr_runnable_component', 'safe'],

            ['cccr_sort_order', 'integer'],

            ['cccr_updated_dt', 'safe'],

            ['cccr_updated_user_id', 'integer'],

            ['cccr_value', 'safe'],
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
            'cccr_component_event_id' => $this->cccr_component_event_id,
            'cccr_sort_order' => $this->cccr_sort_order,
            'cccr_enabled' => $this->cccr_enabled,
            'cccr_created_user_id' => $this->cccr_created_user_id,
            'cccr_updated_user_id' => $this->cccr_updated_user_id,
            'date(cccr_created_dt)' => $this->cccr_created_dt,
            'date(cccr_updated_dt)' => $this->cccr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cccr_value', $this->cccr_value])
            ->andFilterWhere(['like', 'cccr_runnable_component', $this->cccr_runnable_component])
            ->andFilterWhere(['like', 'cccr_component_config', $this->cccr_component_config]);

        return $dataProvider;
    }
}
