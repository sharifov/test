<?php

namespace sales\model\leadData\entity;

use yii\data\ActiveDataProvider;
use sales\model\leadData\entity\LeadData;

class LeadDataSearch extends LeadData
{
    public function rules(): array
    {
        return [
            ['ld_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ld_field_key', 'safe'],

            ['ld_field_value', 'safe'],

            ['ld_id', 'integer'],

            ['ld_lead_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ld_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ld_id' => $this->ld_id,
            'ld_lead_id' => $this->ld_lead_id,
            'DATE(ld_created_dt)' => $this->ld_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ld_field_key', $this->ld_field_key])
            ->andFilterWhere(['like', 'ld_field_value', $this->ld_field_value]);

        return $dataProvider;
    }
}
