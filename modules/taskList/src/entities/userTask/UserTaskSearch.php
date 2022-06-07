<?php

namespace modules\taskList\src\entities\userTask;

use yii\data\ActiveDataProvider;
use modules\taskList\src\entities\userTask\UserTask;

/**
 * Class UserTaskSearch
 */
class UserTaskSearch extends UserTask
{
    /* TODO:: add dateRange logic */

    public function rules(): array
    {
        return [
            [['ut_start_dt', 'ut_end_dt', 'ut_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['ut_target_object', 'safe'],

            ['ut_id', 'integer'],
            ['ut_month', 'integer'],
            ['ut_priority', 'integer'],
            ['ut_status_id', 'integer'],
            ['ut_target_object_id', 'integer'],
            ['ut_task_list_id', 'integer'],
            ['ut_user_id', 'integer'],
            ['ut_year', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ut_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ut_id' => $this->ut_id,
            'ut_user_id' => $this->ut_user_id,
            'ut_target_object_id' => $this->ut_target_object_id,
            'ut_task_list_id' => $this->ut_task_list_id,
            'DATE(ut_start_dt)' => $this->ut_start_dt,
            'DATE(ut_end_dt)' => $this->ut_end_dt,
            'ut_priority' => $this->ut_priority,
            'ut_status_id' => $this->ut_status_id,
            'DATE(ut_created_dt)' => $this->ut_created_dt,
            'ut_year' => $this->ut_year,
            'ut_month' => $this->ut_month,
        ]);

        $query->andFilterWhere(['like', 'ut_target_object', $this->ut_target_object]);

        return $dataProvider;
    }
}
