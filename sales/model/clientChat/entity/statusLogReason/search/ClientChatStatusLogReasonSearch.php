<?php

namespace sales\model\clientChat\entity\statusLogReason\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason;

class ClientChatStatusLogReasonSearch extends ClientChatStatusLogReason
{
    public function rules(): array
    {
        return [
            ['cslr_action_reason_id', 'integer'],

            ['cslr_comment', 'safe'],

            ['cslr_id', 'integer'],

            ['cslr_status_log_id', 'integer'],
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
            'cslr_id' => $this->cslr_id,
            'cslr_status_log_id' => $this->cslr_status_log_id,
            'cslr_action_reason_id' => $this->cslr_action_reason_id,
        ]);

        $query->andFilterWhere(['like', 'cslr_comment', $this->cslr_comment]);

        return $dataProvider;
    }
}
