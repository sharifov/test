<?php

namespace sales\model\phoneLine\phoneLine\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;

class PhoneLineSearch extends PhoneLine
{
    public function rules(): array
    {
        return [
            ['line_allow_in', 'integer'],

            ['line_allow_out', 'integer'],

            ['line_created_dt', 'safe'],

            ['line_created_user_id', 'integer'],

            ['line_dep_id', 'integer'],

            ['line_enabled', 'integer'],

            ['line_id', 'integer'],

            ['line_language_id', 'safe'],

            ['line_name', 'safe'],

            ['line_personal_user_id', 'integer'],

            ['line_project_id', 'integer'],

            ['line_settings_json', 'safe'],

            ['line_updated_dt', 'safe'],

            ['line_updated_user_id', 'integer'],

            ['line_uvm_id', 'integer'],
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
            'line_id' => $this->line_id,
            'line_project_id' => $this->line_project_id,
            'line_dep_id' => $this->line_dep_id,
            'line_personal_user_id' => $this->line_personal_user_id,
            'line_uvm_id' => $this->line_uvm_id,
            'line_allow_in' => $this->line_allow_in,
            'line_allow_out' => $this->line_allow_out,
            'line_enabled' => $this->line_enabled,
            'line_created_user_id' => $this->line_created_user_id,
            'line_updated_user_id' => $this->line_updated_user_id,
            'date_format(line_created_dt, "%Y-%m-%d")' => $this->line_created_dt,
            'date_format(line_updated_dt, "%Y-%m-%d")' => $this->line_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'line_name', $this->line_name])
            ->andFilterWhere(['like', 'line_language_id', $this->line_language_id])
            ->andFilterWhere(['like', 'line_settings_json', $this->line_settings_json]);

        return $dataProvider;
    }
}
