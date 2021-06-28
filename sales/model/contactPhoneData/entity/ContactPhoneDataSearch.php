<?php

namespace sales\model\contactPhoneData\entity;

use yii\data\ActiveDataProvider;
use sales\model\contactPhoneData\entity\ContactPhoneData;

class ContactPhoneDataSearch extends ContactPhoneData
{
    public function rules(): array
    {
        return [
            ['cpd_cpl_id', 'integer'],
            [['cpd_created_dt', 'cpd_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['cpd_key', 'string'],
            ['cpd_value', 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cpd_cpl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cpd_cpl_id' => $this->cpd_cpl_id,
            'DATE(cpd_created_dt)' => $this->cpd_created_dt,
            'DATE(cpd_updated_dt)' => $this->cpd_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cpd_key', $this->cpd_key])
            ->andFilterWhere(['like', 'cpd_value', $this->cpd_value]);

        return $dataProvider;
    }
}
