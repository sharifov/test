<?php

namespace src\model\leadRequest\entity;

use yii\data\ActiveDataProvider;
use src\model\leadRequest\entity\LeadRequest;

/**
 * Class LeadRequestSearch
 */
class LeadRequestSearch extends LeadRequest
{
    public function rules(): array
    {
        return [
            ['lr_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['lr_id', 'integer'],

            [['lr_job_id', 'lr_lead_id'], 'integer'],

            ['lr_json_data', 'safe'],

            ['lr_type', 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lr_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lr_id' => $this->lr_id,
            'lr_job_id' => $this->lr_job_id,
            'lr_lead_id' => $this->lr_lead_id,
            'DATE(lr_created_dt)' => $this->lr_created_dt,
            'lr_type' => $this->lr_type,
        ]);

        $query->andFilterWhere(['like', 'lr_json_data', $this->lr_json_data]);

        return $dataProvider;
    }
}
