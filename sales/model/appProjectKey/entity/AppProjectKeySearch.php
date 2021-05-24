<?php

namespace sales\model\appProjectKey\entity;

use yii\data\ActiveDataProvider;
use sales\model\appProjectKey\entity\AppProjectKey;

class AppProjectKeySearch extends AppProjectKey
{
    public function rules(): array
    {
        return [
            ['apk_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['apk_created_user_id', 'integer'],

            ['apk_id', 'integer'],

            ['apk_key', 'safe'],

            ['apk_project_id', 'integer'],

            ['apk_project_source_id', 'integer'],

            ['apk_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['apk_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['apk_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'apk_id' => $this->apk_id,
            'apk_project_id' => $this->apk_project_id,
            'apk_project_source_id' => $this->apk_project_source_id,
            'DATE(apk_created_dt)' => $this->apk_created_dt,
            'DATE(apk_updated_dt)' => $this->apk_updated_dt,
            'apk_created_user_id' => $this->apk_created_user_id,
            'apk_updated_user_id' => $this->apk_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'apk_key', $this->apk_key]);

        return $dataProvider;
    }
}
