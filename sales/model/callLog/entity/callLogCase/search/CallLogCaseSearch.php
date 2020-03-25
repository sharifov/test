<?php

namespace sales\model\callLog\entity\callLogCase\search;

use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLogCase\CallLogCase;

class CallLogCaseSearch extends CallLogCase
{
    public function rules(): array
    {
        return [
            [['clc_cl_id', 'clc_case_id'], 'integer'],
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
        ]);

        return $dataProvider;
    }
}
