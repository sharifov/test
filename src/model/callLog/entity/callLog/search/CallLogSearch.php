<?php

namespace src\model\callLog\entity\callLog\search;

use common\models\Call;
use common\models\Client;
use common\models\Employee;
use common\models\PhoneBlacklist;
use common\models\ProjectEmployeeAccess;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use src\auth\Auth;
use src\model\callLog\entity\callLog\CallLogCategory;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogCase\CallLogCase;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\callLog\entity\callLogRecord\CallLogRecord;
use src\model\callNote\entity\CallNote;
use src\model\voip\phoneDevice\device\VoipDevice;
use yii\data\ActiveDataProvider;
use src\model\callLog\entity\callLog\CallLog;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use src\helpers\query\QueryHelper;

/**
 * Class CallLogSearch
 *
 * @property int|null $lead_id
 * @property int|null $case_id
 * @property int $clq_queue_time
 * @property int $clq_access_count
 * @property string $createTimeRange
 * @property string $createTimeStart
 * @property string $createTimeEnd
 * @property string $callNote
 */
class CallLogSearch extends CallLog
{
    public $lead_id;
    public $case_id;
    public $clq_queue_time;
    public $clq_access_count;

    public $createTimeRange;
    public $createTimeZone;
    public $createTimeStart;
    public $createTimeEnd;

    public $projectIds = [];
    public $statusIds = [];
    public $typesIds = [];
    public $categoryIds = [];
    public $departmentIds = [];
    public $userGroupIds = [];
    public $callDurationFrom;
    public $callDurationTo;
    public $callNote;
    public $userID;

    public $reportTimezone;
    public $defaultUserTz;
    public $timeFrom;
    public $timeTo;
    public $callDepId;
    public $userGroupId;
    public $minTalkTime;
    public $maxTalkTime;
    public $reportCreateTimeRange;

    public const CREATE_TIME_START_DEFAULT_RANGE = '-3 month';

    public function rules(): array
    {
        return [
            ['cl_type_id', 'integer'],
            ['cl_type_id', 'in', 'range' => array_keys(CallLogType::getList())],

            ['cl_category_id', 'integer'],
            ['cl_category_id', 'in', 'range' => array_keys(Call::SOURCE_LIST)],

            ['cl_status_id', 'integer'],
            ['cl_status_id', 'in', 'range' => array_keys(CallLogStatus::getList())],

            ['cl_is_transfer', 'boolean'],

            [['cl_phone_from', 'cl_phone_to', 'callNote'], 'string'],

            ['cl_phone_list_id', 'integer'],

            [['cl_id', 'cl_group_id', 'cl_category_id', 'cl_is_transfer', 'cl_phone_list_id', 'cl_user_id', 'cl_department_id', 'cl_project_id', 'cl_client_id'], 'integer'],

            [['cl_call_sid', 'cl_phone_from', 'cl_phone_to'], 'string'],

            [['cl_price'], 'number'],

            [['cl_call_created_dt', 'cl_call_finished_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['lead_id', 'integer'],
            ['case_id', 'integer'],

            ['cl_duration', 'integer'],

            [['clq_access_count', 'clq_queue_time'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['clq_access_count', 'clq_queue_time'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['callDurationFrom', 'callDurationTo'], 'integer'],
            [['projectIds', 'statusIds', 'typesIds', 'categoryIds', 'departmentIds', 'userGroupIds'], 'each', 'rule' => ['integer']],
            [['reportTimezone', 'timeFrom', 'timeTo'], 'string'],
            [['callDepId', 'userGroupId', 'minTalkTime', 'maxTalkTime', 'userID'], 'integer'],
            [['reportCreateTimeRange', 'createTimeStart', 'createTimeEnd'], 'safe'],

            ['cl_stir_status', 'string'],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $userTimezoneName = Auth::user()->userParams->up_timezone;
        $userTimezone = strlen($userTimezoneName) ? new \DateTimeZone($userTimezoneName) : new \DateTimeZone('UTC');
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone($userTimezone);
        $this->createTimeRange = ($currentDate->modify(self::CREATE_TIME_START_DEFAULT_RANGE))->format('Y-m-d') . ' 00:00 - ' . $currentDate->format('Y-m-d') . ' 23:59';
        $this->createTimeZone = $currentDate->format('P e');
    }

    /*public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }*/

    public function search($params, Employee $user): ActiveDataProvider
    {
        $filters = $params['CallLogSearch'] ?? [];
        unset($filters['createTimeRange']);
        unset($filters['createTimeStart']);
        unset($filters['createTimeEnd']);

        $query = static::find()
            ->with(['project', 'department', 'phoneList', 'user'])
            ->joinWith(['callLogLead.lead', 'callLogCase.case', 'queue', 'record']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount' => !array_filter($filters) ? $this->callLogGridItemsCount($params) : null,
            'sort' => ['defaultOrder' => ['cl_call_created_dt' => SORT_DESC]],
        ]);

        /*if(!array_filter(isset($params['CallLogSearch']) ? $params['CallLogSearch'] : [])) {
            $dataProvider->totalCount = static::find()->count();
        }*/

        $dataProvider->sort->attributes['lead_id'] = [
            'asc' => ['cll_lead_id' => SORT_ASC],
            'desc' => ['cll_lead_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['case_id'] = [
            'asc' => ['clc_case_id' => SORT_ASC],
            'desc' => ['clc_case_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['clq_access_count'] = [
            'asc' => ['clq_access_count' => SORT_ASC],
            'desc' => ['clq_access_count' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['clq_queue_time'] = [
            'asc' => ['clq_queue_time' => SORT_ASC],
            'desc' => ['clq_queue_time' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->cl_call_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'cl_call_created_dt', $this->cl_call_created_dt, $user->timezone);
        }

        if ($this->cl_call_finished_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'cl_call_finished_dt', $this->cl_call_finished_dt, $user->timezone);
        }

        if ($this->clq_queue_time || $this->clq_queue_time === 0) {
            $query->andWhere(['clq_queue_time' => $this->clq_queue_time]);
        }

        if ($this->clq_access_count || $this->clq_access_count === 0) {
            $query->andWhere(['clq_access_count' => $this->clq_access_count]);
        }

        if ($this->cl_group_id) {
            $query->andWhere([
                'OR',
                ['cl_id' => $this->cl_group_id],
                ['cl_group_id' => $this->cl_group_id],
            ]);
        }

        if ($this->createTimeRange) {
            $date = explode(' - ', $this->createTimeRange);
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($date[0]));
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($date[1]));
            $query->andWhere(['between', 'cl_call_created_dt', $dateTimeStart, $dateTimeEnd]);
            //$query->andWhere(['IN', 'cl_year', range(date("Y", strtotime($dateTimeStart)), date("Y", strtotime($dateTimeEnd)))]);
            $query->from([new Expression(static::tableName() . ' PARTITION(' . QueryHelper::getPartitionsByYears($date[0], $date[1]) . ') ')])->alias('cl');
        }

        if ($this->userID) {
            $query->andWhere(['cl_user_id' => $this->userID]);
        }

        if ($this->projectIds) {
            $query->andWhere(['cl_project_id' => $this->projectIds]);
        }

        if ($this->departmentIds) {
            $query->andWhere(['cl_department_id' => $this->departmentIds]);
        }

        if ($this->userGroupIds) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id) as cl_user_id'])->where(['ugs_group_id' => $this->userGroupIds])->asArray()->all();
            if ($userIdsByGroup) {
                $query->andWhere(['in', ['cl_user_id'], $userIdsByGroup]);
            }
        }

        if (!empty($this->minTalkTime) && empty($this->maxTalkTime)) {
            $query->andWhere(['>=', 'clr_duration', $this->minTalkTime]);
        } elseif (!empty($this->maxTalkTime) && empty($this->minTalkTime)) {
            $query->andWhere(['<=', 'clr_duration', $this->maxTalkTime]);
        } elseif (!empty($this->minTalkTime) && !empty($this->maxTalkTime)) {
            $query->andWhere(['between', 'clr_duration', $this->minTalkTime, $this->maxTalkTime]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cl_id' => $this->cl_id,
            'cl_type_id' => $this->cl_type_id,
            'cl_category_id' => $this->cl_category_id,
            'cl_is_transfer' => $this->cl_is_transfer,
            'cl_duration' => $this->cl_duration,
            'cl_phone_list_id' => $this->cl_phone_list_id,
            'cl_user_id' => $this->cl_user_id,
            'cl_department_id' => $this->cl_department_id,
            'cl_project_id' => $this->cl_project_id,
            'cl_status_id' => $this->cl_status_id,
            'cl_client_id' => $this->cl_client_id,
            'cl_price' => $this->cl_price,
            'cll_lead_id' => $this->lead_id,
            'clc_case_id' => $this->case_id,
            'cl_stir_status' => $this->cl_stir_status,
        ]);

        $query->andFilterWhere(['like', 'cl_call_sid', $this->cl_call_sid])
            ->andFilterWhere(['like', 'cl_phone_from', $this->cl_phone_from])
            ->andFilterWhere(['like', 'cl_phone_to', $this->cl_phone_to]);

        /*
         * Filtering call logs by department id of current user
         *
         * Adds new selection conditions into the `$query`:
         * > `... AND (('cl_department_id' IN $DEPARTMENT_LIST$) OR ('cl_project_id' IN $PROJECT_LIST$))`
         *
         * When:
         * - $DEPARTMENT_LIST$ is a subquery getting user department
         * - $PROJECT_LIST$ is a subquery getting user projects
         *
         */
        /** @var Query $filterByDepartmentSubQuery */
        $filterByDepartmentSubQuery = new Query();
        $filterByDepartmentSubQuery
            ->select('ud_dep_id')
            ->from(UserDepartment::tableName())
            ->where(['ud_user_id' => $user->getPrimaryKey()]);
        /** @var Query $filterByProjectSubQuery */
        $filterByProjectSubQuery = new Query();
        $filterByProjectSubQuery
            ->select('project_id')
            ->from(ProjectEmployeeAccess::tableName())
            ->where(['employee_id' => $user->getPrimaryKey()]);
        $query->andWhere(['OR', ['IN', 'cl_department_id', $filterByDepartmentSubQuery], ['IN', 'cl_project_id', $filterByProjectSubQuery]]);

        return $dataProvider;
    }

    private function callLogGridItemsCount($params)
    {
        $query = static::find();
        $this->load($params);

        if ($this->createTimeRange) {
            $date = explode(' - ', $this->createTimeRange);
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($date[0]));
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($date[1]));
            $query->andWhere(['between', 'cl_call_created_dt', $dateTimeStart, $dateTimeEnd]);
            $query->from([new Expression(static::tableName() . ' PARTITION(' . QueryHelper::getPartitionsByYears($date[0], $date[1]) . ') ')])->alias('cl');
        }
        return $query->count();
    }

    public function getCallHistory(int $userId): ActiveDataProvider
    {
        $query = static::find();

        $q = new Query();
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $query->select([
            'cl_id',
            'cl_call_sid',
            'call_log.cl_type_id',
            'cl_phone_from',
            'cl_phone_to',
            'cl_client_id',
            'cl_call_created_dt',
            'cl_status_id',
            'cl_duration',
            'cl_category_id',
            'cll_lead_id as lead_id',
            'clc_case_id as case_id',
            'call_log.cl_project_id',
            'cl_department_id',
            'cl_category_id',
            'cl_client_id',
        ]);

        $clientTableName = Client::tableName();
        $query->addSelect([
            "if (" . $clientTableName . ".is_company = 1, " . $clientTableName . ".company_name, if (" . $clientTableName . ".first_name is not null, if (" . $clientTableName . ".last_name is not null, concat(" . $clientTableName . ".first_name, ' ', " . $clientTableName . ".last_name), " . $clientTableName . ".first_name), null)) as client_name",
            'cn_note as callNote'
        ]);

        $devicePrefix = VoipDevice::getClientPrefix();
        $query->addSelect([
            "IF(
              call_log.cl_type_id = 1,
              if (cl_phone_to regexp '" . $devicePrefix . "' = 1, REGEXP_SUBSTR(cl_phone_to, '[[:digit:]]+'), null),
              if (cl_phone_from regexp '" . $devicePrefix . "' = 1, REGEXP_SUBSTR(cl_phone_from, '[[:digit:]]+'), null)
            ) AS user_id"
        ]);
        $query->addSelect([
            "phone_blacklist.pbl_enabled as in_blacklist"
        ]);
        $query->leftJoin($clientTableName, $clientTableName . '.id = cl_client_id');
        $query->leftJoin(CallLogLead::tableName(), 'cll_cl_id = cl_id');
        $query->leftJoin(CallLogCase::tableName(), 'clc_cl_id = cl_id');
        $query->leftJoin(CallNote::tableName(), new Expression('cn_id = (select cn_id from call_note where cn_call_id = cl_id order by cn_created_dt desc limit 1)'));
        $query->leftJoin(PhoneBlacklist::tableName(), new Expression('pbl_phone = (if (call_log.cl_type_id = 1, cl_phone_to, cl_phone_from)) and pbl_enabled = 1 and pbl_expiration_date > now()'));
        $query->andWhere(['cl_user_id' => $userId]);
        $query->andWhere(['call_log.cl_type_id' => [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT]]);
        $query->groupBy([
            'cl_id',
            'cl_call_sid',
            'call_log.cl_type_id',
            'cl_phone_from',
            'cl_phone_to',
            'cl_client_id',
            'cl_call_created_dt',
            'cl_status_id',
            'cl_duration',
            'callNote',
            'client_name',
            'user_id',
            'cl_category_id',
            'cll_lead_id',
            'clc_case_id',
            'call_log.cl_project_id',
            'cl_department_id',
            'cl_client_id',
        ]);
        $query->orderBy(['cl_call_created_dt' => SORT_DESC]);

        $userTableName = Employee::tableName();
        $q->select([
            'cl_id',
            'cl_call_sid',
            'cl_type_id',
            'cl_phone_from',
            'cl_phone_to',
            'cl_client_id',
            'cl_call_created_dt',
            'cl_status_id',
            'cl_duration',
            'callNote',
            'client_name',
            'user_id',
            'cl_category_id',
            'lead_id',
            'case_id',
            'cl_project_id',
            'cl_department_id',
            'cl_client_id',
            'in_blacklist',
            new Expression('if (cl_type_id = 1, cl_phone_to, cl_phone_from) as phone_to_blacklist'),
            Employee::tableName() . '.nickname as nickname',
            new Expression('if (client_name is not null, client_name, if (cl_type_id = 1, if(' . $userTableName . '.nickname is not null, ' . $userTableName . '.nickname, cl_phone_to), if(' . $userTableName . '.nickname is not null, ' . $userTableName . '.nickname, cl_phone_from))) as formatted'),
        ])
            ->from($query)
            ->leftJoin(Employee::tableName(), Employee::tableName() . '.id = user_id');

//      VarDumper::dump($q->createCommand()->getRawSql());die;

        return $dataProvider;
    }

    public function searchMyCalls($params, $id): ActiveDataProvider
    {
        $this->load($params);

        $query = static::find();
        $query->where(['cl_user_id' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cl_id' => SORT_DESC]],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /*if ($this->cl_call_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cl_call_created_dt', $this->cl_call_created_dt, $user->timezone);
        }*/

        if ($this->createTimeStart && $this->createTimeEnd) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart));
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd));
            $query->andWhere(['between', 'cl_call_created_dt', $dateTimeStart, $dateTimeEnd]);
        }

        if ($this->projectIds) {
            $query->andWhere(['cl_project_id' => $this->projectIds]);
        }

        if ($this->statusIds) {
            $query->andWhere(['cl_status_id' => $this->statusIds]);
        }

        if ($this->typesIds) {
            $query->andWhere(['cl_type_id' => $this->typesIds]);
        }
        if ($this->categoryIds) {
            $query->andWhere(['cl_category_id' => $this->categoryIds]);
        }

        if ($this->departmentIds) {
            $query->andWhere(['cl_department_id' => $this->departmentIds]);
        }

        if ($this->callDurationFrom) {
            $query->andWhere(['>=', 'cl_duration', $this->callDurationFrom]);
        }

        if ($this->callDurationTo) {
            $query->andWhere(['<=', 'cl_duration', $this->callDurationTo]);
        }

        $query->andFilterWhere([
            'cl_id' => $this->cl_id,
            'cl_project_id' => $this->cl_project_id,
            'cl_department_id' => $this->cl_department_id,
            'cl_type_id' => $this->cl_type_id,
            'cl_category_id' => $this->cl_category_id,
            'cl_status_id' => $this->cl_status_id,
            'cl_client_id' => $this->cl_client_id,
            'DATE(cl_call_created_dt)' => $this->cl_call_created_dt,
            'cl_stir_status' => $this->cl_stir_status,
        ]);

        $query->andFilterWhere(['like', 'cl_phone_from', $this->cl_phone_from])
            ->andFilterWhere(['like', 'cl_phone_to', $this->cl_phone_to]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param $user Employee
     * @return ArrayDataProvider
     * @throws \yii\db\Exception
     */
    public function searchCallsReport($params, $user): ArrayDataProvider
    {
        $this->load($params);
        $timezone = $user->timezone;

        if ($this->reportTimezone == null) {
            $this->defaultUserTz = $timezone;
        } else {
            $timezone = $this->reportTimezone;
            $this->defaultUserTz = $this->reportTimezone;
        }

        if ($this->timeTo == "") {
            $differenceTimeToFrom = "24:00";
        } else {
            if ((strtotime($this->timeTo) - strtotime($this->timeFrom)) <= 0) {
                $differenceTimeToFrom = sprintf("%02d:00", (strtotime("24:00") - strtotime(sprintf("%02d:00", abs((strtotime($this->timeTo) - strtotime($this->timeFrom))) / 3600))) / 3600);
            } else {
                $differenceTimeToFrom = sprintf("%02d:00", (strtotime($this->timeTo) - strtotime($this->timeFrom)) / 3600);
            }
        }

        if ($this->reportCreateTimeRange != null) {
            $dates = explode(' - ', $this->reportCreateTimeRange);
            $hourSub = date('G', strtotime($dates[0]));
            $timeSub = date('G', strtotime($this->timeFrom));

            $date_from = Employee::convertToUTC(strtotime($dates[0]) - ($hourSub * 3600), $this->defaultUserTz);
            $date_to = Employee::convertToUTC(strtotime($dates[1]), $this->defaultUserTz);
            $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
            $utcOffsetDST = Employee::getUtcOffsetDst($timezone, $date_from) ?? date('P');
        } else {
            $timeSub = date('G', strtotime(date('00:00')));

            $date_from = Employee::convertToUTC(strtotime(date('Y-m-d 00:00')), $this->defaultUserTz);
            $date_to = Employee::convertToUTC(strtotime(date('Y-m-d 23:59')), $this->defaultUserTz);
            $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
            $utcOffsetDST = Employee::getUtcOffsetDst($timezone, $date_from) ?? date('P');
        }

        if (!empty($this->minTalkTime) && empty($this->maxTalkTime)) {
            $queryByLogRecordDuration = ' AND clr_duration >=' . $this->minTalkTime;
        } elseif (!empty($this->maxTalkTime) && empty($this->minTalkTime)) {
            $queryByLogRecordDuration = ' AND clr_duration <=' . $this->maxTalkTime;
        } elseif (!empty($this->minTalkTime) && !empty($this->maxTalkTime)) {
            $queryByLogRecordDuration = ' AND clr_duration BETWEEN ' . $this->minTalkTime . ' AND ' . $this->maxTalkTime;
        } else {
            $queryByLogRecordDuration = '';
        }

        $query = new Query();
        $query->leftJoin(CallLogRecord::tableName(), static::tableName() . '.cl_id =' . CallLogRecord::tableName() . '.clr_cl_id');
        $query->select(['cl_user_id, DATE(CONVERT_TZ(DATE_SUB(cl_call_created_dt, INTERVAL ' . $timeSub . ' HOUR), "+00:00", "' . $utcOffsetDST . '")) AS createdDate,
            COALESCE(SUM(IF(cl_type_id = ' . CallLogType::OUT . ' OR cl_type_id = ' . CallLogType::IN . ' OR cl_category_id = ' . CallLogCategory::REDIAL_CALL . ', clr_duration, 0)), 0) as totalTalkTime,            
            SUM(IF(cl_type_id = ' . CallLogType::OUT . ' AND (cl_category_id <> ' . CallLogCategory::REDIAL_CALL . ' OR cl_category_id IS NULL), cl_duration, 0)) as outCallsDuration,
            COALESCE(SUM(IF(cl_type_id = ' . CallLogType::OUT . $queryByLogRecordDuration . ' AND (cl_category_id <> ' . CallLogCategory::REDIAL_CALL . ' OR cl_category_id IS NULL), clr_duration, 0)), 0) as outCallsTalkTime,
            SUM(IF(cl_type_id = ' . CallLogType::OUT . ' AND (cl_category_id <> ' . CallLogCategory::REDIAL_CALL . ' OR cl_category_id IS NULL), 1, 0)) as totalOutCalls,
            COALESCE(SUM(IF(cl_type_id = ' . CallLogType::OUT . ' AND (cl_category_id <> ' . CallLogCategory::REDIAL_CALL . ' OR cl_category_id IS NULL) AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ', clr_duration, 0)), 0) as outCallsCompletedDuration,
            SUM(IF(cl_type_id = ' . CallLogType::OUT . ' AND (cl_category_id <> ' . CallLogCategory::REDIAL_CALL . ' OR cl_category_id IS NULL) AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ', 1, 0)) as outCallsCompleted,
            
            COALESCE(SUM(IF(cl_type_id = ' . CallLogType::IN . $queryByLogRecordDuration . ', clr_duration, 0)), 0) as inCallsDuration,
            SUM(IF(cl_type_id = ' . CallLogType::IN . ' AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ', 1, 0)) as inCallsCompleted,
            SUM(IF(cl_type_id = ' . CallLogType::IN . ' AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ' AND cl_category_id = ' . CallLogCategory::DIRECT_CALL . ', 1, 0)) as inCallsDirectLine,
            SUM(IF(cl_type_id = ' . CallLogType::IN . ' AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ' AND cl_category_id = ' . CallLogCategory::GENERAL_LINE . ', 1, 0)) as inCallsGeneralLine,
            SUM(IF(cl_category_id = ' . CallLogCategory::REDIAL_CALL . ', cl_duration, 0)) as redialCallsDuration,
            SUM(IF(cl_category_id = ' . CallLogCategory::REDIAL_CALL . $queryByLogRecordDuration . ', clr_duration, 0)) as redialCallsTalkTime,
            SUM(IF(cl_category_id = ' . CallLogCategory::REDIAL_CALL . ', 1, 0)) as redialCallsTotalAttempts,
            SUM(IF(cl_category_id = ' . CallLogCategory::REDIAL_CALL . ' AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ', 1, 0)) as redialCallsCompleted,
            SUM(IF(cl_category_id = ' . CallLogCategory::REDIAL_CALL . ' AND cl_status_id = ' . CallLogStatus::COMPLETE . $queryByLogRecordDuration . ', clr_duration, 0)) as redialCallsCompleteTalkTime           
        ']);

        $query->from([new Expression(static::tableName() . ' PARTITION(' . QueryHelper::getPartitionsByYears($date_from, $date_to) . ') ')]);
        $query->where('cl_call_created_dt ' . $between_condition);
        $query->andWhere('cl_user_id IS NOT NULL');
        $query->andWhere('TIME(CONVERT_TZ(DATE_SUB(cl_call_created_dt, INTERVAL ' . $timeSub . ' HOUR), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '")');

        if (!empty($this->cl_user_id)) {
            $query->andWhere('cl_user_id=' . $this->cl_user_id);
        }

        if (isset($params['CallLogSearch']['callDepId']) && $params['CallLogSearch']['callDepId'] != "") {
            $query->andWhere('cl_department_id= ' . $params['CallLogSearch']['callDepId']);
        }

        if (!empty($this->cl_project_id)) {
            $query->andWhere('cl_project_id=' . $this->cl_project_id);
        }

        if (!empty($this->userGroupId)) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where('ugs_group_id = ' . $this->userGroupId);
            $query->andWhere(['cl_user_id' => $userIdsByGroup]);
        }

        $query->groupBy(['cl_user_id', 'createdDate']);

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $model) {
            if (
                $model['totalTalkTime'] == 0 &&
                $model['outCallsDuration'] == 0 &&
                $model['outCallsTalkTime'] == 0 &&
                $model['totalOutCalls'] == 0 &&
                $model['outCallsCompletedDuration'] == 0 &&
                $model['outCallsCompleted'] == 0 &&
                //$model['outCallsNoAnswer'] == 0 &&
                $model['inCallsDuration'] == 0 &&
                $model['inCallsCompleted'] == 0 &&
                $model['inCallsDirectLine'] == 0 &&
                $model['inCallsGeneralLine'] == 0 &&
                $model['redialCallsDuration'] == 0 &&
                $model['redialCallsTalkTime'] == 0 &&
                $model['redialCallsTotalAttempts'] == 0 &&
                $model['redialCallsCompleted'] == 0 &&
                $model['redialCallsCompleteTalkTime'] == 0
            ) {
                unset($data[$key]);
            }
        }

        $paramsData = [
            'allModels' => $data,
            'sort' => [
                //'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'cl_user_id',
                    'createdDate',
                    'totalTalkTime',
                    'outCallsDuration',
                    'outCallsTalkTime',
                    'totalOutCalls',
                    'outCallsCompletedDuration',
                    'outCallsCompleted',
                    //'outCallsNoAnswer',
                    'inCallsDuration',
                    'inCallsCompleted',
                    'inCallsDirectLine',
                    'inCallsGeneralLine',
                    'redialCallsDuration',
                    'redialCallsTalkTime',
                    'redialCallsTotalAttempts',
                    'redialCallsCompleted',
                    'redialCallsCompleteTalkTime',
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return new ArrayDataProvider($paramsData);
    }

    public function searchCallsGraph($params, $user_id): array
    {
        $query = new Query();
        $query->addSelect(['DATE(cl_call_created_dt) as createdDate,
               SUM(IF(cl_status_id= ' . CallLogStatus::COMPLETE . ', 1, 0)) AS callsComplete,
               SUM(IF(cl_status_id= ' . CallLogStatus::BUSY . ', 1, 0)) AS callsBusy,
               SUM(IF(cl_status_id= ' . CallLogStatus::NOT_ANSWERED . ', 1, 0)) AS callsNotAnswered,
               SUM(IF(cl_status_id= ' . CallLogStatus::FAILED . ', 1, 0)) AS callsFailed,
               SUM(IF(cl_status_id= ' . CallLogStatus::CANCELED . ', 1, 0)) AS callsCanceled,
               SUM(IF(cl_status_id= ' . CallLogStatus::DECLINED . ', 1, 0)) AS callsDeclined
        ']);

        $query->from(static::tableName());
        $query->where('cl_status_id IS NOT NULL');
        $query->andWhere(['cl_user_id' => $user_id]);
        if ($this->createTimeStart && $this->createTimeEnd) {
            $query->andWhere(['>=', 'cl_call_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart))]);
            $query->andWhere(['<=', 'cl_call_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))]);
        }

        $query->groupBy('createdDate');

        return $query->createCommand()->queryAll();
    }
}
