<?php

namespace sales\model\callLog\entity\callLogRecord\search;

use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLogRecord\CallLogRecord;

class CallLogRecordSearch extends CallLogRecord
{
    public function rules(): array
    {
        return [
            [['clr_cl_id', 'clr_duration'], 'integer'],
            [['clr_record_sid'], 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = CallLogRecord::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'clr_cl_id' => $this->clr_cl_id,
            'clr_duration' => $this->clr_duration,
        ]);

        $query->andFilterWhere(['like', 'clr_record_sid', $this->clr_record_sid]);

        return $dataProvider;
    }
}
