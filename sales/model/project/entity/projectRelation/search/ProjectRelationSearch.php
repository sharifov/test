<?php

namespace sales\model\project\entity\projectRelation\search;

use yii\data\ActiveDataProvider;
use sales\model\project\entity\projectRelation\ProjectRelation;

class ProjectRelationSearch extends ProjectRelation
{
    public function rules(): array
    {
        return [
            [['prl_project_id', 'prl_related_project_id', 'prl_created_user_id', 'prl_updated_user_id'], 'integer'],

            [['prl_created_dt', 'prl_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['prl_updated_dt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'prl_project_id' => $this->prl_project_id,
            'prl_related_project_id' => $this->prl_related_project_id,
            'prl_created_user_id' => $this->prl_created_user_id,
            'prl_updated_user_id' => $this->prl_updated_user_id,
            'DATE(prl_created_dt)' => $this->prl_created_dt,
            'DATE(prl_updated_dt)' => $this->prl_updated_dt,
        ]);

        return $dataProvider;
    }
}
