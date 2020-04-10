<?php

namespace sales\model\callLog\entity\callLogQueue\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLogQueue\CallLogQueue;

class CallLogQueueSearch extends CallLogQueue
{
    public function rules(): array
    {
        return [
            [['clq_cl_id', 'clq_queue_time', 'clq_access_count', 'clq_is_transfer'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = CallLogQueue::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'clq_cl_id' => $this->clq_cl_id,
            'clq_queue_time' => $this->clq_queue_time,
            'clq_access_count' => $this->clq_access_count,
            'clq_is_transfer' => $this->clq_is_transfer,
        ]);

        return $dataProvider;
    }
}
