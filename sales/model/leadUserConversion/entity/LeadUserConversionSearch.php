<?php

namespace sales\model\leadUserConversion\entity;

use yii\data\ActiveDataProvider;

class LeadUserConversionSearch extends LeadUserConversion
{
    public function rules(): array
    {
        return [
            ['luc_description', 'string', 'max' => 100],
            ['luc_lead_id', 'integer'],
            ['luc_user_id', 'integer'],
            ['luc_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['luc_created_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['luc_created_dt' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'luc_lead_id' => $this->luc_lead_id,
            'luc_user_id' => $this->luc_user_id,
            'luc_created_user_id' => $this->luc_created_user_id,
            'DATE(luc_created_dt)' => $this->luc_created_dt,
        ]);

        $query->andFilterWhere(['like', 'luc_description', $this->luc_description]);

        return $dataProvider;
    }
}
