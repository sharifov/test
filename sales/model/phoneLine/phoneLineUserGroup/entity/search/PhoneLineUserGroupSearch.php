<?php

namespace sales\model\phoneLine\phoneLineUserGroup\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup;

class PhoneLineUserGroupSearch extends PhoneLineUserGroup
{
    public function rules(): array
    {
        return [
            [['plug_created_dt', 'plug_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['plug_line_id', 'integer'],

            ['plug_ug_id', 'integer'],
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
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'plug_line_id' => $this->plug_line_id,
            'plug_ug_id' => $this->plug_ug_id,
            'date_format(plug_created_dt, "%Y-%m-%d")' => $this->plug_created_dt,
            'date_format(plug_updated_dt, "%Y-%m-%d")' => $this->plug_updated_dt,
        ]);

        return $dataProvider;
    }
}
