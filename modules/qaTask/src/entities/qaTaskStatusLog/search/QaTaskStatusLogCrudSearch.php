<?php

namespace modules\qaTask\src\entities\qaTaskStatusLog\search;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class QaTaskStatusLogCrudSearch extends QaTaskStatusLog
{
    public function rules(): array
    {
        return [
            ['tsl_id', 'integer'],
            ['tsl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['tsl_id' => 'tsl_id']],

            ['tsl_task_id', 'integer'],
            ['tsl_task_id', 'exist', 'skipOnError' => true, 'targetClass' => QaTask::class, 'targetAttribute' => ['tsl_task_id' => 't_id']],

            ['tsl_start_status_id', 'integer'],
            ['tsl_start_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['tsl_end_status_id', 'integer'],
            ['tsl_end_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['tsl_start_dt', 'date', 'format' => 'php:Y-m-d'],

            ['tsl_end_dt', 'date', 'format' => 'php:Y-m-d'],

            ['tsl_duration', 'integer'],

            ['tsl_reason_id', 'integer'],
            ['tsl_reason_id', 'exist', 'skipOnError' => true, 'targetClass' => QaTaskStatusReason::class, 'targetAttribute' => ['tsl_reason_id' => 'tsr_id']],

            ['tsl_description', 'string', 'max' => 255],

            ['tsl_action_id', 'integer'],
            ['tsl_action_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['tsl_action_id', 'in', 'range' => array_keys(QaTaskActions::getList())],

            ['tsl_assigned_user_id', 'integer'],
            ['tsl_assigned_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tsl_assigned_user_id' => 'id']],

            ['tsl_created_user_id', 'integer'],
            ['tsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tsl_created_user_id' => 'id']],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['assignedUser', 'createdUser', 'task', 'reason']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->tsl_start_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tsl_start_dt', $this->tsl_start_dt, $user->timezone);
        }

        if ($this->tsl_end_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tsl_end_dt', $this->tsl_end_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'tsl_id' => $this->tsl_id,
            'tsl_task_id' => $this->tsl_task_id,
            'tsl_start_status_id' => $this->tsl_start_status_id,
            'tsl_end_status_id' => $this->tsl_end_status_id,
            'tsl_duration' => $this->tsl_duration,
            'tsl_action_id' => $this->tsl_action_id,
            'tsl_reason_id' => $this->tsl_reason_id,
            'tsl_assigned_user_id' => $this->tsl_assigned_user_id,
            'tsl_created_user_id' => $this->tsl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'tsl_description', $this->tsl_description]);

        return $dataProvider;
    }
}
