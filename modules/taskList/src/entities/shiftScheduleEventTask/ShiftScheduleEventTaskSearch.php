<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask;

use yii\data\ActiveDataProvider;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;

/**
 * Class ShiftScheduleEventTaskSearch
 */
class ShiftScheduleEventTaskSearch extends ShiftScheduleEventTask
{
    public function rules(): array
    {
        return [
            ['sset_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['sset_event_id', 'integer'],
            ['sset_user_task_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sset_created_dt' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sset_event_id' => $this->sset_event_id,
            'sset_user_task_id' => $this->sset_user_task_id,
            'DATE(sset_created_dt)' => $this->sset_created_dt,
        ]);

        return $dataProvider;
    }
}
