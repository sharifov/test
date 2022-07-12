<?php

namespace src\model\leadBusinessExtraQueue\entity;

use yii\data\ActiveDataProvider;

class LeadBusinessExtraQueueSearch extends LeadBusinessExtraQueue
{
    public function rules(): array
    {
        return [
            [['lbeq_created_dt', 'lbeq_expiration_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['lbeq_lead_id', 'integer'],

            ['lbeq_lbeqr_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lbeq_lead_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lbeq_lead_id' => $this->lbeq_lead_id,
            'lbeq_lbeqr_id' => $this->lbeq_lbeqr_id,
            'DATE(lbeq_expiration_dt)' => $this->lbeq_expiration_dt,
            'DATE(lbeq_created_dt)' => $this->lbeq_expiration_dt,
        ]);

        return $dataProvider;
    }
}
