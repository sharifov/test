<?php

namespace sales\model\leadOrder\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\leadOrder\entity\LeadOrder;

class LeadOrderSearch extends LeadOrder
{
    public function rules(): array
    {
        return [
            ['lo_create_dt', 'safe'],

            ['lo_created_user_id', 'integer'],

            ['lo_lead_id', 'integer'],

            ['lo_order_id', 'integer'],
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
            'lo_order_id' => $this->lo_order_id,
            'lo_lead_id' => $this->lo_lead_id,
            'lo_create_dt' => $this->lo_create_dt,
            'lo_created_user_id' => $this->lo_created_user_id,
        ]);

        return $dataProvider;
    }
}
