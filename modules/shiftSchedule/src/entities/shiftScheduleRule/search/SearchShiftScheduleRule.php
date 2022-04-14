<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRule\search;

use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use yii\data\ActiveDataProvider;

class SearchShiftScheduleRule extends ShiftScheduleRule
{
    public function rules(): array
    {
        return [
            ['ssr_created_dt', 'safe'],

            ['ssr_created_user_id', 'integer'],

            ['ssr_cron_expression', 'safe'],

            ['ssr_cron_expression_exclude', 'safe'],

            ['ssr_duration_time', 'integer'],

            ['ssr_enabled', 'integer'],

            ['ssr_end_time_loc', 'safe'],

            ['ssr_end_time_utc', 'safe'],

            ['ssr_id', 'integer'],

            ['ssr_shift_id', 'integer'],

            ['ssr_start_time_loc', 'safe'],

            ['ssr_start_time_utc', 'safe'],

            ['ssr_timezone', 'safe'],

            ['ssr_title', 'safe'],

            ['ssr_updated_dt', 'safe'],

            ['ssr_updated_user_id', 'integer'],
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
            'ssr_id' => $this->ssr_id,
            'ssr_shift_id' => $this->ssr_shift_id,
            'ssr_start_time_loc' => $this->ssr_start_time_loc,
            'ssr_end_time_loc' => $this->ssr_end_time_loc,
            'ssr_duration_time' => $this->ssr_duration_time,
            'ssr_enabled' => $this->ssr_enabled,
            'ssr_start_time_utc' => $this->ssr_start_time_utc,
            'ssr_end_time_utc' => $this->ssr_end_time_utc,
            'date(ssr_created_dt)' => $this->ssr_created_dt,
            'date(ssr_updated_dt)' => $this->ssr_updated_dt,
            'ssr_created_user_id' => $this->ssr_created_user_id,
            'ssr_updated_user_id' => $this->ssr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ssr_title', $this->ssr_title])
            ->andFilterWhere(['like', 'ssr_timezone', $this->ssr_timezone])
            ->andFilterWhere(['like', 'ssr_cron_expression', $this->ssr_cron_expression])
            ->andFilterWhere(['like', 'ssr_cron_expression_exclude', $this->ssr_cron_expression_exclude]);

        return $dataProvider;
    }
}
