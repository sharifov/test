<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRule\search;

use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use yii\data\ActiveDataProvider;

class SearchShiftScheduleRule extends ShiftScheduleRule
{
    public ?string $shift_name = null;
    public ?string $shift_category_id = null;

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
            ['shift_name', 'safe'],
            ['shift_category_id', 'safe'],
            ['ssr_updated_dt', 'safe'],
            ['ssr_updated_user_id', 'integer'],
            ['ssr_sst_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find()
            ->select('*')
            ->leftJoin(Shift::tableName(), 'shift_schedule_rule.ssr_shift_id = shift.sh_id')
            ->leftJoin(ShiftCategory::tableName(), 'shift.sh_category_id = shift_category.sc_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ssr_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $dataProvider->sort->attributes['shift_name'] = [
            'asc' => ['shift.sh_name' => SORT_ASC, ],
            'desc' => ['shift.sh_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['shift_category_id'] = [
            'asc' => ['shift_category.sc_name' => SORT_ASC],
            'desc' => ['shift_category.sc_name' => SORT_DESC],
        ];

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
            'ssr_sst_id' => $this->ssr_sst_id,
        ]);

        $query->andFilterWhere(['like', 'shift.sh_name', $this->shift_name])
            ->andFilterWhere(['like', 'shift_category.sc_id', $this->shift_category_id])
            ->andFilterWhere(['like', 'ssr_title', $this->ssr_title])
            ->andFilterWhere(['like', 'ssr_timezone', $this->ssr_timezone])
            ->andFilterWhere(['like', 'ssr_cron_expression', $this->ssr_cron_expression])
            ->andFilterWhere(['like', 'ssr_cron_expression_exclude', $this->ssr_cron_expression_exclude]);

        return $dataProvider;
    }
}
