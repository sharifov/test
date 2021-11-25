<?php

namespace sales\model\clientData\entity;

use yii\data\ActiveDataProvider;
use sales\model\clientData\entity\ClientData;

class ClientDataSearch extends ClientData
{
    public function rules(): array
    {
        return [
            ['cd_client_id', 'integer'],
            [['cd_created_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['cd_field_value', 'safe'],
            ['cd_id', 'integer'],
            ['cd_key_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cd_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cd_id' => $this->cd_id,
            'cd_client_id' => $this->cd_client_id,
            'cd_key_id' => $this->cd_key_id,
            'DATE(cd_created_dt)' => $this->cd_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cd_field_value', $this->cd_field_value]);

        return $dataProvider;
    }
}
