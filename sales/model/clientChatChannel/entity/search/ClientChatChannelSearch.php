<?php

namespace sales\model\clientChatChannel\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatChannel\entity\ClientChatChannel;

class ClientChatChannelSearch extends ClientChatChannel
{
    public function rules(): array
    {
        return [
            ['ccc_created_dt', 'safe'],

            ['ccc_created_user_id', 'integer'],

            ['ccc_dep_id', 'integer'],

            ['ccc_disabled', 'integer'],

            ['ccc_priority', 'integer'],

            ['ccc_id', 'integer'],

            ['ccc_name', 'safe'],

            ['ccc_project_id', 'integer'],

            ['ccc_ug_id', 'integer'],

            ['ccc_updated_dt', 'safe'],

            ['ccc_updated_user_id', 'integer'],
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
            'ccc_id' => $this->ccc_id,
            'ccc_project_id' => $this->ccc_project_id,
            'ccc_dep_id' => $this->ccc_dep_id,
            'ccc_ug_id' => $this->ccc_ug_id,
            'ccc_disabled' => $this->ccc_disabled,
            'ccc_priority' => $this->ccc_priority,
            'date_format(ccc_created_dt, "%Y-%m-%d")' => $this->ccc_created_dt,
            'date_format(ccc_updated_dt, "%Y-%m-%d")' => $this->ccc_updated_dt,
            'ccc_created_user_id' => $this->ccc_created_user_id,
            'ccc_updated_user_id' => $this->ccc_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ccc_name', $this->ccc_name]);

        return $dataProvider;
    }
}
