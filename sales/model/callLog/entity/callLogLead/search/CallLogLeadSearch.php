<?php

namespace sales\model\callLog\entity\callLogLead\search;

use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLogLead\CallLogLead;

class CallLogLeadSearch extends CallLogLead
{
    public function rules(): array
    {
        return [
            [['cll_cl_id', 'cll_lead_id', 'cll_lead_flow_id'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = CallLogLead::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cll_cl_id' => $this->cll_cl_id,
            'cll_lead_id' => $this->cll_lead_id,
            'cll_lead_flow_id' => $this->cll_lead_flow_id,
        ]);

        return $dataProvider;
    }
}
