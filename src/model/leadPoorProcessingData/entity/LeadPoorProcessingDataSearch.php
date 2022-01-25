<?php

namespace src\model\leadPoorProcessingData\entity;

use yii\data\ActiveDataProvider;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;

class LeadPoorProcessingDataSearch extends LeadPoorProcessingData
{
    public function rules(): array
    {
        return [
            ['lppd_description', 'string'],
            ['lppd_enabled', 'integer'],
            ['lppd_id', 'integer'],
            ['lppd_key', 'string'],
            ['lppd_minute', 'integer'],
            ['lppd_name', 'string'],
            ['lppd_params_json', 'safe'],
            ['lppd_updated_dt', 'date', 'format' => 'php:Y-m-d'],
            ['lppd_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lppd_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lppd_id' => $this->lppd_id,
            'lppd_enabled' => $this->lppd_enabled,
            'lppd_minute' => $this->lppd_minute,
            'DATE(lppd_updated_dt)' => $this->lppd_updated_dt,
            'lppd_updated_user_id' => $this->lppd_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'lppd_key', $this->lppd_key])
            ->andFilterWhere(['like', 'lppd_name', $this->lppd_name])
            ->andFilterWhere(['like', 'lppd_description', $this->lppd_description])
            ->andFilterWhere(['like', 'lppd_params_json', $this->lppd_params_json]);

        return $dataProvider;
    }
}
