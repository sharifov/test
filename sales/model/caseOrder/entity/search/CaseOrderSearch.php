<?php

namespace sales\model\caseOrder\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\caseOrder\entity\CaseOrder;

class CaseOrderSearch extends CaseOrder
{
    public function rules(): array
    {
        return [
            ['co_case_id', 'integer'],

            ['co_create_dt', 'safe'],

            ['co_created_user_id', 'integer'],

            ['co_order_id', 'integer'],
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
            'co_order_id' => $this->co_order_id,
            'co_case_id' => $this->co_case_id,
            'co_create_dt' => $this->co_create_dt,
            'co_created_user_id' => $this->co_created_user_id,
        ]);

        return $dataProvider;
    }
}
