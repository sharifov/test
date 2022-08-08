<?php

namespace modules\taskList\src\entities\userTask;

use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use src\helpers\app\AppHelper;
use src\helpers\app\DBHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * UserTaskSearch
 */
class UserTaskSearch extends UserTask
{
    public string $createTimeRange = '';
    public string $createTimeStart = '';
    public string $createTimeEnd = '';

    public string $clientStartDate = '';
    public string $clientEndDate = '';
//    public string $startedDateRange = '';
//    public string $endedDateRange = '';

    private string $defaultDTStart;
    private string $defaultDTEnd;

    public function __construct(int $defaultMonth = 1, string $formatDt = 'Y-m-d', array $config = [])
    {
        $this->defaultDTEnd = (new \DateTime())->format($formatDt);
        $this->defaultDTStart = (new \DateTimeImmutable())
            ->modify('-' . abs($defaultMonth) . ' months')->format($formatDt);

        parent::__construct($config);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['ut_start_dt', 'ut_end_dt', 'ut_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['ut_target_object', 'safe'],

            ['ut_id', 'integer'],

            ['ut_priority', 'integer'],
            ['ut_status_id', 'integer'],
            ['ut_target_object_id', 'integer'],
            ['ut_task_list_id', 'integer'],
            ['ut_user_id', 'integer'],

            ['ut_year', 'integer'],
            ['ut_month', 'integer'],

            [['createTimeRange'], 'default', 'value' => $this->defaultDTStart . ' - ' . $this->defaultDTEnd],

            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['createTimeStart', 'createTimeEnd'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserTask::find();

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

        if ($this->createTimeRange) {
            try {
                $dTStart = (new \DateTimeImmutable($this->createTimeStart))->setTime(0, 0);
                $dTEnd = (new \DateTime($this->createTimeEnd))->setTime(23, 59, 59);
                $sqlDTRestriction = DBHelper::yearMonthRestrictionQuery(
                    $dTStart,
                    $dTEnd,
                    'ut_year',
                    'ut_month'
                );
                $query->where($sqlDTRestriction);
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['model'] = ArrayHelper::toArray($this);
                \Yii::warning($message, 'UserTaskSearch:search:Exception');
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['model'] = ArrayHelper::toArray($this);
                \Yii::error($message, 'UserTaskSearch:search:Throwable');
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ut_id' => $this->ut_id,
            'ut_user_id' => $this->ut_user_id,
            'ut_target_object_id' => $this->ut_target_object_id,
            'ut_task_list_id' => $this->ut_task_list_id,
            'ut_priority' => $this->ut_priority,
            'ut_status_id' => $this->ut_status_id,
            'ut_year' => $this->ut_year,
            'ut_month' => $this->ut_month,
        ]);

        if ($this->ut_start_dt) {
            $query->andFilterWhere(['>=', 'ut_start_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt))])
                ->andFilterWhere(['<=', 'ut_start_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt) + 3600 * 24)]);
        }
        if ($this->ut_end_dt) {
            $query->andFilterWhere(['>=', 'ut_end_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_end_dt))])
                ->andFilterWhere(['<=', 'ut_end_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_end_dt) + 3600 * 24)]);
        }
        if ($this->ut_created_dt) {
            $query->andFilterWhere(['>=', 'ut_created_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_created_dt))])
                ->andFilterWhere(['<=', 'ut_created_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere(['like', 'ut_target_object', $this->ut_target_object]);

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return ActiveDataProvider
     */
    public function searchByUserId(
        array $params,
        int $userId,
        ?string $startDate = null,
        ?string $endDate = null
    ): ActiveDataProvider {
        $query = UserTask::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ut_priority' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }


        $query->andWhere(['ut_user_id' => $userId]);

        if (!empty($startDate) && !empty($endDate)) {
            $this->clientStartDate = $startDate;
            $this->clientEndDate = $endDate;

            $startDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
            $endDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));

            $query->andWhere([
                'OR',
                ['between', 'ut_start_dt', $startDateTime, $endDateTime],
                ['between', 'ut_end_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'ut_start_dt', $startDateTime],
                    ['<=', 'ut_end_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'ut_start_dt', $startDateTime],
                    ['>=', 'ut_end_dt', $endDateTime]
                ]
            ]);
        }


        if ($startDateTime && $endDateTime) {
            try {
                $dTStart = (new \DateTimeImmutable($startDateTime))->setTime(0, 0);
                $dTEnd = (new \DateTime($endDateTime))->setTime(23, 59, 59);
                $sqlDTRestriction = DBHelper::yearMonthRestrictionQuery(
                    $dTStart,
                    $dTEnd,
                    'ut_year',
                    'ut_month'
                );
                $query->andWhere($sqlDTRestriction);
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['model'] = ArrayHelper::toArray($this);
                \Yii::warning($message, 'UserTaskSearch:search:Exception');
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['model'] = ArrayHelper::toArray($this);
                \Yii::error($message, 'UserTaskSearch:search:Throwable');
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ut_id' => $this->ut_id,
            'ut_user_id' => $this->ut_user_id,
            'ut_target_object_id' => $this->ut_target_object_id,
            'ut_task_list_id' => $this->ut_task_list_id,
            'ut_priority' => $this->ut_priority,
            'ut_status_id' => $this->ut_status_id,
            'ut_year' => $this->ut_year,
            'ut_month' => $this->ut_month,
        ]);

        if ($this->ut_start_dt) {
            $query->andFilterWhere(['>=', 'ut_start_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt))])
                ->andFilterWhere(['<=', 'ut_start_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt) + 3600 * 24)]);
        }
        if ($this->ut_end_dt) {
            $query->andFilterWhere(['>=', 'ut_end_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_end_dt))])
                ->andFilterWhere(['<=', 'ut_end_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_end_dt) + 3600 * 24)]);
        }
        if ($this->ut_created_dt) {
            $query->andFilterWhere(['>=', 'ut_created_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_created_dt))])
                ->andFilterWhere(['<=', 'ut_created_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere(['like', 'ut_target_object', $this->ut_target_object]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param int $scheduleEventId
     * @return ActiveDataProvider
     */
    public function searchByShiftScheduleEventId(
        array $params,
        int $scheduleEventId
    ): ActiveDataProvider {
        $query = UserTask::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ut_priority' => SORT_DESC]],
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->innerJoinWith(['shiftScheduleEventTasks']);
        $query->andWhere(['sset_event_id' => $scheduleEventId]);


        // grid filtering conditions
        $query->andFilterWhere([
            'ut_id' => $this->ut_id,
            'ut_user_id' => $this->ut_user_id,
            'ut_target_object_id' => $this->ut_target_object_id,
            'ut_task_list_id' => $this->ut_task_list_id,
            'ut_priority' => $this->ut_priority,
            'ut_status_id' => $this->ut_status_id,
            'ut_year' => $this->ut_year,
            'ut_month' => $this->ut_month,
        ]);

        if ($this->ut_start_dt) {
            $query->andFilterWhere(['>=', 'ut_start_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt))])
                ->andFilterWhere(['<=', 'ut_start_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt) + 3600 * 24)]);
        }
        if ($this->ut_end_dt) {
            $query->andFilterWhere(['>=', 'ut_end_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_end_dt))])
                ->andFilterWhere(['<=', 'ut_end_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_end_dt) + 3600 * 24)]);
        }
        if ($this->ut_created_dt) {
            $query->andFilterWhere(['>=', 'ut_created_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_created_dt))])
                ->andFilterWhere(['<=', 'ut_created_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere(['like', 'ut_target_object', $this->ut_target_object]);

        return $dataProvider;
    }

    public function searchByTargetObjectAndTargetObjectId(string $targetObject, int $targetObjectId, array $params): ActiveDataProvider
    {
        $query = UserTask::find()
            ->where([
                'AND',
                ['ut_target_object' => $targetObject],
                ['ut_target_object_id' => $targetObjectId],
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ut_created_dt' => SORT_DESC]],
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if ($this->ut_start_dt) {
            $query->andFilterWhere(['>=', 'ut_start_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt))])
                ->andFilterWhere(['<=', 'ut_start_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ut_start_dt) + 3600 * 24)]);
        }

        return $dataProvider;
    }
}
