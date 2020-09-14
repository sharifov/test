<?php

namespace sales\model\phoneLine\phoneLineUserAssign\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign;

class PhoneLineUserAssignSearch extends PhoneLineUserAssign
{
    public function rules(): array
    {
        return [
            ['plus_allow_in', 'integer'],

            ['plus_allow_out', 'integer'],

            ['plus_created_dt', 'safe'],

            ['plus_created_user_id', 'integer'],

            ['plus_enabled', 'integer'],

            ['plus_line_id', 'integer'],

            ['plus_settings_json', 'safe'],

            ['plus_updated_dt', 'safe'],

            ['plus_updated_user_id', 'integer'],

            ['plus_user_id', 'integer'],

            ['plus_uvm_id', 'integer'],
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
            'plus_line_id' => $this->plus_line_id,
            'plus_user_id' => $this->plus_user_id,
            'plus_allow_in' => $this->plus_allow_in,
            'plus_allow_out' => $this->plus_allow_out,
            'plus_uvm_id' => $this->plus_uvm_id,
            'plus_enabled' => $this->plus_enabled,
            'plus_created_user_id' => $this->plus_created_user_id,
            'plus_updated_user_id' => $this->plus_updated_user_id,
            'plus_created_dt' => $this->plus_created_dt,
            'plus_updated_dt' => $this->plus_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'plus_settings_json', $this->plus_settings_json]);

        return $dataProvider;
    }
}
