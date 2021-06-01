<?php

namespace sales\model\leadDataKey\entity;

use yii\data\ActiveDataProvider;
use sales\model\leadDataKey\entity\LeadDataKey;

class LeadDataKeySearch extends LeadDataKey
{
    public function rules(): array
    {
        return [
            ['ldk_enable', 'boolean'],

            ['ldk_id', 'integer'],

            [['ldk_key', 'ldk_name'], 'string'],

            [['ldk_created_dt', 'ldk_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            [['ldk_created_user_id', 'ldk_updated_user_id'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ldk_id' => $this->ldk_id,
            'ldk_enable' => $this->ldk_enable,
            'DATE(ldk_created_dt)' => $this->ldk_created_dt,
            'DATE(ldk_updated_dt)' => $this->ldk_updated_dt,
            'ldk_created_user_id' => $this->ldk_created_user_id,
            'ldk_updated_user_id' => $this->ldk_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ldk_key', $this->ldk_key])
            ->andFilterWhere(['like', 'ldk_name', $this->ldk_name]);

        return $dataProvider;
    }
}
