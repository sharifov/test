<?php

namespace src\model\leadUserData\entity;

use yii\data\ActiveDataProvider;
use src\model\leadUserData\entity\LeadUserData;

class LeadUserDataSearch extends LeadUserData
{
    public function rules(): array
    {
        return [
            ['lud_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['lud_id', 'integer'],
            ['lud_lead_id', 'integer'],
            ['lud_type_id', 'integer'],
            ['lud_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lud_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lud_id' => $this->lud_id,
            'lud_type_id' => $this->lud_type_id,
            'lud_lead_id' => $this->lud_lead_id,
            'lud_user_id' => $this->lud_user_id,
            'DATE(lud_created_dt)' => $this->lud_created_dt,
        ]);

        return $dataProvider;
    }
}
