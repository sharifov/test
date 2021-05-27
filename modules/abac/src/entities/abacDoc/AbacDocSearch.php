<?php

namespace modules\abac\src\entities\abacDoc;

use yii\data\ActiveDataProvider;
use modules\abac\src\entities\abacDoc\AbacDoc;

class AbacDocSearch extends AbacDoc
{
    public function rules(): array
    {
        return [
            ['ad_action', 'string', 'max' => 50],
            ['ad_description', 'string', 'max' => 50],
            ['ad_object', 'string', 'max' => 50],
            ['ad_subject', 'string', 'max' => 50],
            ['ad_file', 'string', 'max' => 100],

            ['ad_line', 'integer'],
            ['ad_id', 'integer'],

            ['ad_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['ad_id' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ad_id' => $this->ad_id,
            'ad_line' => $this->ad_line,
            'DATE(ad_created_dt)' => $this->ad_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ad_file', $this->ad_file])
            ->andFilterWhere(['like', 'ad_subject', $this->ad_subject])
            ->andFilterWhere(['like', 'ad_object', $this->ad_object])
            ->andFilterWhere(['like', 'ad_action', $this->ad_action])
            ->andFilterWhere(['like', 'ad_description', $this->ad_description]);

        return $dataProvider;
    }
}
