<?php

namespace src\model\callLog\entity\callLogCase\search;

use yii\data\ActiveDataProvider;
use src\model\callLog\entity\callLogCase\CallLogCase;

class CallLogCaseSearch extends CallLogCase
{
    public function rules(): array
    {
        return [
            [['clc_cl_id', 'clc_case_id', 'clc_case_status_log_id'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = CallLogCase::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'clc_cl_id' => $this->clc_cl_id,
            'clc_case_id' => $this->clc_case_id,
            'clc_case_status_log_id' => $this->clc_case_status_log_id,
        ]);

        return $dataProvider;
    }
}
