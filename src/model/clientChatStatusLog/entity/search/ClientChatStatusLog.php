<?php

namespace src\model\clientChatStatusLog\entity\search;

use yii\data\ActiveDataProvider;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog as ClientChatStatusLogModel;

class ClientChatStatusLog extends ClientChatStatusLogModel
{
    public function rules(): array
    {
        return [
            ['csl_cch_id', 'integer'],

            ['csl_description', 'safe'],

            ['csl_from_status', 'integer'],

            ['csl_id', 'integer'],

            ['csl_owner_id', 'integer'],

            [['csl_start_dt', 'csl_end_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['csl_to_status', 'integer'],

            ['csl_user_id', 'integer'],
            ['csl_prev_channel_id', 'integer'],
            ['csl_action_type', 'integer'],

            ['csl_rid', 'string', 'max' => 150],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['csl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'csl_id' => $this->csl_id,
            'csl_cch_id' => $this->csl_cch_id,
            'csl_from_status' => $this->csl_from_status,
            'csl_to_status' => $this->csl_to_status,
            'date_format(csl_start_dt, "%Y-%m-%d")' => $this->csl_start_dt,
            'date_format(csl_end_dt, "%Y-%m-%d")' => $this->csl_end_dt,
            'csl_owner_id' => $this->csl_owner_id,
            'csl_user_id' => $this->csl_user_id,
            'csl_prev_channel_id' => $this->csl_prev_channel_id,
            'csl_action_type' => $this->csl_action_type,
            'csl_rid' => $this->csl_rid,
        ]);

        $query->andFilterWhere(['like', 'csl_description', $this->csl_description]);

        return $dataProvider;
    }
}
