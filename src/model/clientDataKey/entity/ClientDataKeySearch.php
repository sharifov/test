<?php

namespace src\model\clientDataKey\entity;

use yii\data\ActiveDataProvider;
use src\model\clientDataKey\entity\ClientDataKey;

class ClientDataKeySearch extends ClientDataKey
{
    public function rules(): array
    {
        return [
            ['cdk_created_user_id', 'integer'],
            ['cdk_description', 'safe'],
            ['cdk_enable', 'integer'],
            ['cdk_id', 'integer'],
            ['cdk_is_system', 'integer'],
            ['cdk_key', 'safe'],
            ['cdk_name', 'safe'],
            ['cdk_updated_user_id', 'integer'],
            [['cdk_created_dt', 'cdk_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cdk_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cdk_id' => $this->cdk_id,
            'cdk_enable' => $this->cdk_enable,
            'cdk_is_system' => $this->cdk_is_system,
            'DATE(cdk_created_dt)' => $this->cdk_created_dt,
            'DATE(cdk_updated_dt)' => $this->cdk_updated_dt,
            'cdk_created_user_id' => $this->cdk_created_user_id,
            'cdk_updated_user_id' => $this->cdk_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'cdk_key', $this->cdk_key])
            ->andFilterWhere(['like', 'cdk_name', $this->cdk_name])
            ->andFilterWhere(['like', 'cdk_description', $this->cdk_description]);

        return $dataProvider;
    }
}
