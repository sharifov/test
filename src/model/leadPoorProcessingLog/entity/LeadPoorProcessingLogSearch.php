<?php

namespace src\model\leadPoorProcessingLog\entity;

use yii\data\ActiveDataProvider;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;

class LeadPoorProcessingLogSearch extends LeadPoorProcessingLog
{
    public function rules(): array
    {
        return [
            [['lppl_created_dt', 'lppl_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['lppl_id', 'integer'],
            ['lppl_lead_id', 'integer'],
            ['lppl_lppd_id', 'integer'],
            ['lppl_owner_id', 'integer'],
            ['lppl_status', 'integer'],
            ['lppl_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lppl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lppl_id' => $this->lppl_id,
            'lppl_lead_id' => $this->lppl_lead_id,
            'lppl_lppd_id' => $this->lppl_lppd_id,
            'lppl_status' => $this->lppl_status,
            'lppl_owner_id' => $this->lppl_owner_id,
            'DATE(lppl_created_dt)' => $this->lppl_created_dt,
            'DATE(lppl_updated_dt)' => $this->lppl_updated_dt,
            'lppl_updated_user_id' => $this->lppl_updated_user_id,
        ]);

        return $dataProvider;
    }
}
