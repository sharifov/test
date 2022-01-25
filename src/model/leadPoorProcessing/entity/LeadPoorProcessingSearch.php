<?php

namespace src\model\leadPoorProcessing\entity;

use yii\data\ActiveDataProvider;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;

class LeadPoorProcessingSearch extends LeadPoorProcessing
{
    public function rules(): array
    {
        return [
            ['lpp_expiration_dt', 'date', 'format' => 'php:Y-m-d'],

            ['lpp_lead_id', 'integer'],

            ['lpp_lppd_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lpp_lead_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lpp_lead_id' => $this->lpp_lead_id,
            'lpp_lppd_id' => $this->lpp_lppd_id,
            'DATE(lpp_expiration_dt)' => $this->lpp_expiration_dt,
        ]);

        return $dataProvider;
    }
}
