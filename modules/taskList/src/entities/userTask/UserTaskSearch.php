<?php

namespace modules\taskList\src\entities\userTask;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\Lead;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use src\helpers\app\AppHelper;
use src\helpers\app\DBHelper;
use src\validators\DateTimeRangeValidator;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * UserTaskSearch
 */
class UserTaskSearch extends UserTask
{
    public const SEPARATOR_DATE_RANGE = ' - ';

    public $taskListIds;
    public $userTaskStatus;
    public $userTaskEmployee;
    public $userTaskUserGroup;
    public $leadStatus;

    public string $createTimeRange = '';
    public string $createTimeStart = '';
    public string $createTimeEnd = '';
    public string $leadCreateDTRange = '';
    public string $leadCreateTimeStart = '';
    public string $leadCreateTimeEnd = '';

    public string $taskName = '';
    public string $clientStartDate = '';
    public string $clientEndDate = '';

    private string $defaultDTStart;
    private string $defaultDTEnd;
    private string $formatDt;

    public function __construct(int $defaultMonth = 1, string $formatDt = 'Y-m-d', array $config = [])
    {
        $this->formatDt = $formatDt;
        $this->defaultDTEnd = (new \DateTime())->format($this->formatDt);
        $this->defaultDTStart = (new \DateTimeImmutable())
            ->modify('-' . abs($defaultMonth) . ' months')->format($this->formatDt);

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
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'leadCreateDTRange',
                'dateStartAttribute' => 'leadCreateTimeStart',
                'dateEndAttribute' => 'leadCreateTimeEnd',
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

            [['createTimeRange'], 'default', 'value' => $this->defaultDTStart . self::SEPARATOR_DATE_RANGE . $this->defaultDTEnd],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['createTimeStart', 'createTimeEnd'], 'safe'],

            ['taskName', 'string'],

            [['taskListIds'], IsArrayValidator::class],
            [['taskListIds'], 'each', 'rule' => ['in', 'range' => array_keys(TaskList::getListCache())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['userTaskStatus'], IsArrayValidator::class],
            [['userTaskStatus'], 'each', 'rule' => ['in', 'range' => array_keys(self::STATUS_LIST)], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['userTaskEmployee'], IsArrayValidator::class],
            [['userTaskEmployee'], 'each', 'rule' => ['in', 'range' => array_keys(Employee::getActiveUsersList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['userTaskUserGroup'], IsArrayValidator::class],
            [['userTaskUserGroup'], 'each', 'rule' => ['in', 'range' => array_keys(UserGroup::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['leadStatus'], IsArrayValidator::class],
            [['leadStatus'], 'each', 'rule' => ['in', 'range' => array_keys(Lead::STATUS_LIST)], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['leadCreateDTRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['leadCreateDTRange'], DateTimeRangeValidator::class, 'separator' => self::SEPARATOR_DATE_RANGE],
            [['leadCreateTimeStart', 'leadCreateTimeEnd'], 'safe'],
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

        $this->filterSearchQuery($query);

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param int $userId
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return ActiveDataProvider
     */
    public function searchByUserId(
        array $params,
        int $userId,
        ?\DateTime $startDate = null,
        ?\DateTime $endDate = null
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
            $this->clientStartDate = $startDate->format('Y-m-d');
            $this->clientEndDate = $endDate->format('Y-m-d');

            $startDateTime = Employee::convertTimeFromUserDtToUTC($startDate->getTimestamp());
            $endDateTime = Employee::convertTimeFromUserDtToUTC($endDate->getTimestamp());
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


        if (!empty($startDateTime) && !empty($endDateTime)) {
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

        if (!empty($this->taskName)) {
            $query->innerJoin('task_list', 'task_list.tl_id = user_task.ut_task_list_id');
            $query->andFilterWhere(['like', 'tl_title', $this->taskName]);
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

    public function searchIds($params): array
    {
        $query = static::find()->select('ut_id');

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return [];
        }

        $this->filterSearchQuery($query);

        return ArrayHelper::map($query->asArray()->all(), 'ut_id', 'ut_id');
    }

    private function filterSearchQuery(UserTaskScopes $query)
    {
        if ($this->createTimeRange) {
            try {
                $dTStart = new \DateTimeImmutable(date('Y-m-d 00:00:00', $this->createTimeStart));
                $dTEnd = new \DateTime(date('Y-m-d 23:59:59', $this->createTimeEnd));
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

        $this->userTaskRestriction($query);
    }

    public function queryReportSummary(array $queryParams): UserTaskScopes
    {
        $query = UserTask::find();
        $query->addSelect([
            'processingCnt' =>
                new Expression('SUM(CASE WHEN ut_status_id = ' . UserTask::STATUS_PROCESSING . ' THEN 1 ELSE 0 END)'),
            'completeCnt' =>
                new Expression('SUM(CASE WHEN ut_status_id = ' . UserTask::STATUS_COMPLETE . ' THEN 1 ELSE 0 END)'),
            'cancelCnt' =>
                new Expression('SUM(CASE WHEN ut_status_id = ' . UserTask::STATUS_CANCEL . ' THEN 1 ELSE 0 END)'),
            'leadCnt' =>
                new Expression('COUNT(DISTINCT(ut_target_object_id))'),
            'allUserTaskCnt' =>
                new Expression('COUNT(*)'),
        ]);

        $this->load($queryParams);
        if (!$this->validate()) {
            $query->where('0=1');
            return $query;
        }

        if ($this->createTimeRange) {
            $dTStart = new \DateTimeImmutable(date('Y-m-d 00:00:00', $this->createTimeStart));
            $dTEnd = new \DateTime(date('Y-m-d 23:59:59', $this->createTimeEnd));
        } else {
            $dTStart = new \DateTimeImmutable(date('Y-m-d 00:00:00', strtotime($this->defaultDTStart)));
            $dTEnd = new \DateTime(date('Y-m-d 23:59:59', strtotime($this->defaultDTEnd)));
        }

        $query = $this->createTimeRangeRestriction($query, $dTStart, $dTEnd);
        $query = $this->userTaskRestriction($query);
        $query = $this->leadRestriction($query);

        return $query;
    }

    private function userTaskRestriction(UserTaskScopes $query): UserTaskScopes
    {
        if ($this->taskListIds) {
            $query->andWhere(['IN', 'ut_task_list_id', $this->taskListIds]);
        }
        if ($this->userTaskStatus) {
            $query->andWhere(['IN', 'ut_status_id', $this->userTaskStatus]);
        }
        if ($this->userTaskEmployee) {
            $query->andWhere(['IN', 'ut_user_id', $this->userTaskEmployee]);
        }
        if ($this->userTaskUserGroup) {
            $query->innerJoin([
                'userGroupAssign' => UserGroupAssign::find()
                    ->select(['ugs_user_id'])
                    ->andWhere(['IN', 'ugs_group_id', $this->userTaskUserGroup])
                    ->groupBy(['ugs_user_id'])
            ], 'userGroupAssign.ugs_user_id = ut_user_id');
        }

        return $query;
    }

    private function leadRestriction(UserTaskScopes $query): UserTaskScopes
    {
        if ($this->leadStatus) {
            $query->innerJoin([
                'leadSubQuery' => Lead::find()
                    ->select(['id', 'status'])
                    ->andWhere(['IN', 'status', $this->leadStatus])
                    ->groupBy(['id', 'status'])
            ], 'leadSubQuery.id = ut_target_object_id AND ut_target_object = :leadObj', [':leadObj' => TargetObject::TARGET_OBJ_LEAD]);
        }
        if ($this->leadCreateDTRange) {
            try {
                $dTStart = new \DateTimeImmutable(date('Y-m-d 00:00:00', $this->createTimeStart));
                $dTEnd = new \DateTimeImmutable(date('Y-m-d 23:59:59', $this->createTimeEnd));

                $query->andWhere($sqlDTRestriction);
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['model'] = ArrayHelper::toArray($this);
                \Yii::warning($message, 'UserTaskSearch:leadRestriction:Exception');
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['model'] = ArrayHelper::toArray($this);
                \Yii::error($message, 'UserTaskSearch:leadRestriction:Throwable');
            }
        }

        return $query;
    }

    private function createTimeRangeRestriction(
        UserTaskScopes $query,
        \DateTimeImmutable $dTStart,
        \DateTime $dTEnd
    ): UserTaskScopes {
        try {
            $query->andWhere([
                'BETWEEN',
                'ut_start_dt',
                $dTStart->format($this->formatDt),
                $dTEnd->format($this->formatDt)
            ]);
            $query->andWhere(DBHelper::yearMonthRestrictionQuery(
                $dTStart,
                $dTEnd,
                'ut_year',
                'ut_month'
            ));
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['model'] = ArrayHelper::toArray($this);
            \Yii::warning($message, 'UserTaskSearch:createTimeRangeRestriction:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['model'] = ArrayHelper::toArray($this);
            \Yii::error($message, 'UserTaskSearch:createTimeRangeRestriction:Throwable');
        }

        return $query;
    }
}
