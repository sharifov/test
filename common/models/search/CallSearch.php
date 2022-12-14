<?php

namespace common\models\search;

use common\models\ConferenceParticipant;
use common\models\Department;
use common\models\Employee;
use Faker\Provider\DateTime;
use kartik\daterange\DateRangeBehavior;
use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use src\helpers\query\QueryHelper;
use src\model\callLogFilterGuard\entity\CallLogFilterGuard;
use src\repositories\call\CallSearchRepository;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Call;
use common\models\UserGroupAssign;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * CallSearch represents the model behind the search form of `common\models\Call`.
 *
 * @property int $limit
 * @property array $dep_ids
 * @property array $project_ids
 * @property array $statuses
 * @property array $status_ids
 * @property array $ug_ids
 *
 * @property string $createTimeRange
 * @property int $createTimeStart
 * @property int $createTimeEnd
 *
 * @property CallSearchRepository $callSearchRepository
 */
class CallSearch extends Call
{
    public $statuses = [];
    public $status_ids = [];

    public $limit = 0;
    public $supervision_id;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $projectId;
    public $statusId;
    public $callTypeId;
    public $call_duration_from;
    public $call_duration_to;
    public $callDepId;
    public $userGroupId;
    public $reportTimezone;
    public $defaultUserTz;
    public $timeFrom;
    public $timeTo;

    public $dep_ids = [];
    public $project_ids = [];
    public $phoneList = [];

    private $callSearchRepository;

    public $cp_type_id;

    public $clfg_type;
    public $clfg_rate;
    public $clfg_redial_status;

    /**
     * user groups id's
     *
     * @var array
     */
    public $ug_ids = [];


    /**
     * CallSearch constructor.
     * @param array $config
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($config = [])
    {
        $this->callSearchRepository = \Yii::createObject(CallSearchRepository::class);
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['c_id', 'c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_is_new', 'supervision_id', 'limit', 'c_recording_duration',
                'c_source_type_id', 'call_duration_from', 'call_duration_to', 'c_case_id', 'c_client_id', 'c_status_id', 'callDepId', 'userGroupId'], 'integer'],
            [['c_call_sid', 'c_from', 'c_to', 'c_call_status', 'c_forwarded_from', 'c_caller_name', 'c_parent_call_sid', 'c_call_duration', 'c_recording_url', 'c_recording_sid', 'c_sequence_number', 'c_error_message', 'c_price', 'statuses', 'limit', 'projectId', 'statusId', 'callTypeId'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['createTimeStart', 'createTimeEnd'], 'safe'],
            [['ug_ids', 'status_ids', 'dep_ids', 'project_ids'], 'each', 'rule' => ['integer']],
            [['reportTimezone', 'timeFrom', 'timeTo'], 'string'],

            ['c_is_transfer', 'boolean'],
            [['c_queue_start_dt','c_created_dt', 'c_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['c_group_id', 'integer'],

            ['phoneList', 'safe'],

            ['cp_type_id', 'integer'],

            ['c_stir_status', 'string'],

            ['clfg_type', 'integer'],
            ['clfg_type', 'in', 'range' => array_keys(CallLogFilterGuard::TYPE_LIST)],
            ['clfg_rate', 'number'],
            ['clfg_redial_status', 'integer'],
            ['clfg_redial_status', 'in', 'range' => array_keys(self::STATUS_LIST)],
        ];
    }


    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'clfg_type' => 'Log Filter Type',
            'clfg_rate' => 'Log Filter Rate',
            'clfg_redial_status' => 'Log Filter Redial Status',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = $this->callSearchRepository->getSearchQuery($user);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['c_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $query->leftJoin(ConferenceParticipant::tableName(), 'cp_call_id = c_id AND cp_type_id = ' . ConferenceParticipant::TYPE_AGENT . ' AND cp_status_id <> ' . ConferenceParticipant::STATUS_LEAVE . ' AND cp_status_id IS NOT NULL');

        $query->joinWith('callLogFilterGuard');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $dataProvider->setTotalCount(QueryHelper::getQueryCountInvalidModel($this, static::class . 'search' . $user->id, $query, 60));
            return $dataProvider;
        }

        $dateTimeStart = $dateTimeEnd = null;

        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $query->andFilterWhere(['>=', 'c_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'c_created_dt', $dateTimeEnd]);

        if ($this->c_created_dt) {
            $query->andFilterWhere(['>=', 'c_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->c_created_dt))])
                ->andFilterWhere(['<=', 'c_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->c_created_dt) + 3600 * 24)]);
        }

        if ($this->projectId) {
            $query->andFilterWhere(['=', 'c_project_id', $this->projectId]);
        }

        if ($this->clfg_type !== null) {
            $query->andFilterWhere(['=', 'clfg_type', $this->clfg_type]);
        }

        if ($this->clfg_rate !== null) {
            $query->andFilterWhere(['clfg_sd_rate' => $this->clfg_rate]);
        }

        if ($this->clfg_redial_status !== null) {
            $query->andFilterWhere(['=', 'clfg_redial_status', $this->clfg_redial_status]);
        }

        if ($this->statusId) {
            $query->andFilterWhere(['=', 'c_status_id', $this->statusId]);
        }

        if ($this->callTypeId) {
            $query->andFilterWhere(['=', 'c_call_type_id', $this->callTypeId]);
        }

        $query->andFilterWhere(['>=','c_call_duration', $this->call_duration_from]);
        $query->andFilterWhere(['<=','c_call_duration', $this->call_duration_to]);


        if ($this->c_queue_start_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'c_queue_start_dt', $this->c_queue_start_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'c_id' => $this->c_id,
            'c_call_type_id' => $this->c_call_type_id,
            'c_lead_id' => $this->c_lead_id,
            'c_case_id' => $this->c_case_id,
            'c_created_user_id' => $this->c_created_user_id,
            //'c_created_dt' => $this->c_created_dt,
            'c_com_call_id' => $this->c_com_call_id,
            'c_updated_dt' => $this->c_updated_dt,
            'c_project_id' => $this->c_project_id,
            'c_is_new' => $this->c_is_new,
            'c_price' => $this->c_price,
            'c_source_type_id' => $this->c_source_type_id,
            'c_call_sid' => $this->c_call_sid,
            'c_parent_call_sid' => $this->c_parent_call_sid,
            'c_client_id' => $this->c_client_id,
            'c_sequence_number' => $this->c_sequence_number,
            'c_recording_sid' => $this->c_recording_sid,
            'c_status_id' => $this->c_status_id,
            'c_is_transfer' => $this->c_is_transfer,
            'c_group_id' => $this->c_group_id,
            'c_stir_status' => $this->c_stir_status,
        ]);

        $query
            ->andFilterWhere(['like', 'c_from', $this->c_from])
            ->andFilterWhere(['like', 'c_to', $this->c_to])
            ->andFilterWhere(['like', 'c_call_status', $this->c_call_status])
            ->andFilterWhere(['like', 'c_forwarded_from', $this->c_forwarded_from])
            ->andFilterWhere(['like', 'c_caller_name', $this->c_caller_name])
            ->andFilterWhere(['like', 'c_call_duration', $this->c_call_duration])
            ->andFilterWhere(['like', 'c_recording_duration', $this->c_recording_duration])
            ->andFilterWhere(['like', 'c_error_message', $this->c_error_message]);

        $dataProvider->setTotalCount(QueryHelper::getQueryCountValidModel($this, static::class . 'search' . $user->id, $query, 60));

        return $dataProvider;
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function searchAgent($params): ActiveDataProvider
    {
        $query = Call::find();

        // add conditions that should always apply here

        $this->load($params);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['c_id' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 30],
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dateTimeStart = $dateTimeEnd = null;

        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $query->andFilterWhere(['>=', 'c_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'c_created_dt', $dateTimeEnd]);


        $query->andFilterWhere(['=','DATE(c_created_dt)', $this->c_created_dt]);

        $query->andWhere(['IS NOT', 'c_parent_id', null]);


        // grid filtering conditions
        $query->andFilterWhere([
            'c_id' => $this->c_id,
            'c_call_type_id' => $this->c_call_type_id,
            'c_lead_id' => $this->c_lead_id,
            'c_case_id' => $this->c_case_id,
            'c_created_user_id' => $this->c_created_user_id,
            'c_com_call_id' => $this->c_com_call_id,
            'c_updated_dt' => $this->c_updated_dt,
            'c_project_id' => $this->c_project_id,
            'c_is_new' => $this->c_is_new,
            'c_source_type_id' => $this->c_source_type_id,
            'c_call_sid' => $this->c_call_sid,
            'c_parent_call_sid' => $this->c_parent_call_sid,
            'c_call_status' => $this->c_call_status,
            'c_client_id' => $this->c_client_id,
            'c_status_id' => $this->c_status_id,
            'c_sequence_number' => $this->c_sequence_number,
            'c_stir_status' => $this->c_stir_status,
        ]);

        $query
            ->andFilterWhere(['like', 'c_from', $this->c_from])
            ->andFilterWhere(['like', 'c_to', $this->c_to])
            //->andFilterWhere(['like', 'c_call_status', $this->c_call_status])
            ->andFilterWhere(['like', 'c_forwarded_from', $this->c_forwarded_from])
            ->andFilterWhere(['like', 'c_caller_name', $this->c_caller_name])
            ->andFilterWhere(['like', 'c_call_duration', $this->c_call_duration])
            ->andFilterWhere(['like', 'c_recording_duration', $this->c_recording_duration])
            ->andFilterWhere(['like', 'c_error_message', $this->c_error_message]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchUserCallMap($params): ActiveDataProvider
    {
        $query = static::find()->select('*')->andWhere(['<>', 'c_call_type_id', Call::CALL_TYPE_JOIN]);

        $query->leftJoin(ConferenceParticipant::tableName(), 'cp_call_id = c_id AND cp_type_id = ' . ConferenceParticipant::TYPE_AGENT  . ' AND cp_status_id <> ' . ConferenceParticipant::STATUS_LEAVE  . ' AND cp_status_id IS NOT NULL');

        $this->load($params);

        //$query->limit(5);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['c_id' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : [
                'pageSize' => 100,
            ]
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        /*$query->andWhere(['c_call_status' => [Call::CALL_STATUS_RINGING]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_IN_PROGRESS]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_QUEUE]]);*/

        $query->andWhere(['or',
            ['c_parent_id' => null],
            ['c_status_id' => [Call::STATUS_DELAY, Call::STATUS_QUEUE]]
        ]);

        if ($this->status_ids) {
            $query->andWhere(['c_status_id' => $this->status_ids]);
        }

        if ($this->dep_ids) {
            $query->andWhere(['c_dep_id' => $this->dep_ids]);
        }

        if ($this->project_ids) {
            $query->andWhere(['c_project_id' => $this->project_ids]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                //->join('JOIN', 'user_department', 'ud_user_id = ugs_user_id and ud_dep_id <> :depId', ['depId' => 'ud_dep_id'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->with(['cProject', 'cLead', /*'cLead.leadFlightSegments',*/ 'cCreatedUser', 'cDep', 'callUserAccesses', 'cuaUsers', 'cugUgs', 'calls']);

//        VarDumper::dump($query->createCommand()->getRawSql());die;

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return array|Call[]
     */
    public function searchMonitorIncomingCalls(array $params): array
    {
        $query = static::find()
            ->select('*')
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['c_source_type_id' => [Call::SOURCE_GENERAL_LINE, Call::SOURCE_REDIRECT_CALL]]);

        $query->leftJoin(ConferenceParticipant::tableName(), 'cp_call_id = c_id AND cp_type_id = ' . ConferenceParticipant::TYPE_AGENT  . ' AND cp_status_id <> ' . ConferenceParticipant::STATUS_LEAVE  . ' AND cp_status_id IS NOT NULL');

        $this->load($params);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return [];
        }

        /*$query->andWhere(['c_call_status' => [Call::CALL_STATUS_RINGING]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_IN_PROGRESS]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_QUEUE]]);*/

//        $query->andWhere(['or',
//            ['c_parent_id' => null],
//            ['c_status_id' => [Call::STATUS_DELAY, Call::STATUS_QUEUE]],
//        ]);

        if ($this->status_ids) {
            $query->andWhere(['c_status_id' => $this->status_ids]);
        }

        if ($this->dep_ids) {
            $query->andWhere(['c_dep_id' => $this->dep_ids]);
        }

        if ($this->project_ids) {
            $query->andWhere(['c_project_id' => $this->project_ids]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                //->join('JOIN', 'user_department', 'ud_user_id = ugs_user_id and ud_dep_id <> :depId', ['depId' => 'ud_dep_id'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->with(['cProject', 'cLead', /*'cLead.leadFlightSegments',*/ 'cCreatedUser', 'cDep', 'callUserAccesses', 'cuaUsers', 'cugUgs', 'calls']);

        $query->orderBy(['c_queue_start_dt' => SORT_DESC]);

        return $query->all();
    }

    public function searchRealtimeUserCallMap($params)
    {
        $this->load($params);

        $query = Call::find()->alias('c');
        $query->select(['c.c_id', 'c.c_call_type_id', 'c.c_source_type_id', 'c.c_from', 'c.c_to', 'c.c_status_id', 'c.c_parent_id', 'c.c_call_duration',
            'c.c_lead_id', 'c.c_case_id', 'c.c_created_dt', 'c.c_updated_dt', 'c.c_created_user_id', 'c.c_call_duration',
            'name as project_name', 'ce.username', 'dep_name', 'c.c_recording_sid',
            'group_concat(cau.username SEPARATOR "-") as cua_user_names', 'concat(cls.first_name, " ", cls.last_name) as full_name', 'l.gid', 'cs_gid',
            'group_concat(cua_user_id SEPARATOR "-") as cua_user_ids', 'group_concat(cua_status_id SEPARATOR "-") as cua_status_ids'
        ]);
        $query->groupBy(['c.c_id']);
        $query->orderBy(['c.c_id' => SORT_DESC]);


        $query->andWhere(['or',
            ['c.c_parent_id' => null],
            ['c.c_status_id' => [Call::STATUS_DELAY, Call::STATUS_QUEUE, Call::STATUS_IN_PROGRESS]]
        ]);

        if ($this->status_ids) {
            $query->andWhere(['c.c_status_id' => $this->status_ids]);
        }

        if ($this->dep_ids) {
            $query->andWhere(['c_dep_id' => $this->dep_ids]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->joinWith(['cProject', 'cLead as l', 'cCase', 'cCreatedUser as ce', 'cDep', 'cClient as cls', 'callUserAccesses.cuaUser as cau', 'cugUgs']);

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $row) {
            if ($row['c_created_dt']) {
                $data[$key]['c_created_dt'] = Yii::$app->formatter->asDatetime(strtotime($row['c_created_dt']), 'php: Y-m-d H:i:s');
            }
            if ($row['c_updated_dt']) {
                $data[$key]['c_updated_dt'] = Yii::$app->formatter->asDatetime(strtotime($row['c_updated_dt']), 'php: Y-m-d H:i:s');
            }
        }

        return $data;
    }

    /**
     * @param $params
     * @param $user Employee
     * @return ArrayDataProvider
     * @throws \Exception
     */
    public function searchCallsStats($params, $user): ArrayDataProvider
    {
        $this->load($params);
        $timezone = $user->timezone;

        if ($this->reportTimezone == null) {
            $this->defaultUserTz = $timezone;
        } else {
            $timezone = $this->reportTimezone;
            $this->defaultUserTz = $this->reportTimezone;
        }

        /*if ($this->timeTo == ""){
            $differenceTimeToFrom  = "24:00";
        } else {
            if((strtotime($this->timeTo) - strtotime($this->timeFrom)) <= 0){
                $differenceTimeToFrom = sprintf("%02d:00",(strtotime("24:00") - strtotime(sprintf("%02d:00", abs((strtotime($this->timeTo) - strtotime($this->timeFrom)) ) / 3600))) / 3600);
            } else {
                $differenceTimeToFrom =  sprintf("%02d:00", (strtotime($this->timeTo) - strtotime($this->timeFrom)) / 3600);
            }
        }*/

        if ($this->createTimeRange != null) {
            $dates = explode(' - ', $this->createTimeRange);
            $hourSub = date('G', strtotime($dates[0]));
            //$timeSub = date('G', strtotime($this->timeFrom));

            $date_from = Employee::convertToUTC(strtotime($dates[0]) - ($hourSub * 3600), $this->defaultUserTz);
            $date_to = Employee::convertToUTC(strtotime($dates[1]), $this->defaultUserTz);
            $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
        //$utcOffsetDST = Employee::getUtcOffsetDst($timezone, $date_from) ?? date('P');
        } else {
            //$timeSub = date('G', strtotime(date('00:00')));

            $date_from = Employee::convertToUTC(strtotime(date('Y-m-d 00:00') . ' -2 days'), $this->defaultUserTz);
            $date_to = Employee::convertToUTC(strtotime(date('Y-m-d 23:59')), $this->defaultUserTz);
            $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
            //$utcOffsetDST = Employee::getUtcOffsetDst($timezone, $date_from) ?? date('P');
        }

        if (!empty($this->call_duration_from) && empty($this->call_duration_to)) {
            $queryByDuration = ' AND c_call_duration >=' . $this->call_duration_from;
        } elseif (!empty($this->call_duration_to) && empty($this->call_duration_from)) {
            $queryByDuration = ' AND c_call_duration <=' . $this->call_duration_to;
        } elseif (!empty($this->call_duration_from) && !empty($this->call_duration_to)) {
            $queryByDuration = ' AND c_call_duration BETWEEN ' . $this->call_duration_from . ' AND ' . $this->call_duration_to;
        } else {
            $queryByDuration = '';
        }

        $query = new Query();
        $query->select(['c_created_user_id,
        
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_OUT . ' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> ' . self::SOURCE_REDIAL_CALL . ' OR c_source_type_id IS NULL), c_call_duration, 0)) AS outgoingCallsDuration,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_OUT . ' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> ' . self::SOURCE_REDIAL_CALL . ' OR c_source_type_id IS NULL), 1, 0)) AS outgoingCalls,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_OUT . ' AND c_status_id = ' . self::STATUS_COMPLETED . ' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> ' . self::SOURCE_REDIAL_CALL . ' OR c_source_type_id IS NULL) ' . $queryByDuration . ', 1, 0)) AS outgoingCallsCompleted,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_OUT . ' AND c_status_id = ' . self::STATUS_NO_ANSWER . ' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> ' . self::SOURCE_REDIAL_CALL . ' OR c_source_type_id IS NULL), 1, 0)) AS outgoingCallsNoAnswer,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_OUT . ' AND c_status_id = ' . self::STATUS_BUSY . ' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> ' . self::SOURCE_REDIAL_CALL . ' OR c_source_type_id IS NULL), 1, 0)) AS outgoingCallsBusy,
        
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_IN . ' AND c_status_id = ' . self::STATUS_COMPLETED . ' AND c_parent_call_sid IS NOT NULL, c_call_duration, 0)) AS incomingCallsDuration,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_IN . ' AND c_status_id = ' . self::STATUS_COMPLETED . ' AND c_parent_call_sid IS NOT NULL ' . $queryByDuration . ', 1, 0)) AS incomingCompletedCalls,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_IN . ' AND c_status_id = ' . self::STATUS_COMPLETED . ' AND c_parent_call_sid IS NOT NULL AND c_source_type_id = ' . self::SOURCE_DIRECT_CALL . ', 1, 0)) AS incomingDirectLine,
        SUM(IF(c_call_type_id = ' . self::CALL_TYPE_IN . ' AND c_status_id = ' . self::STATUS_COMPLETED . ' AND c_parent_call_sid IS NOT NULL AND c_source_type_id <> ' . self::SOURCE_DIRECT_CALL . ', 1, 0)) AS incomingGeneralLine,
        
        SUM(IF(c_source_type_id = ' . self::SOURCE_REDIAL_CALL . ' AND c_status_id = ' . self::STATUS_COMPLETED . ' AND c_parent_call_sid IS NOT NULL , 1, 0)) AS redialCallsDuration,
        SUM(IF(c_source_type_id = ' . self::SOURCE_REDIAL_CALL . ' AND c_parent_call_sid IS NOT NULL, 1, 0)) AS totalAttempts,
        SUM(IF(c_source_type_id = ' . self::SOURCE_REDIAL_CALL . ' AND c_status_id = ' . self::STATUS_COMPLETED . '  AND c_parent_call_sid IS NOT NULL ' . $queryByDuration . ', 1, 0)) AS redialCompleted           
            
        ']);
        $query->from('call');
        $query->where('c_created_dt ' . $between_condition);
        $query->andWhere('c_created_user_id IS NOT NULL');
        //$query->andWhere('TIME(CONVERT_TZ(DATE_SUB(c_created_dt, INTERVAL '. $timeSub .' HOUR), "+00:00", "'. $utcOffsetDST. '")) <= TIME("'.$differenceTimeToFrom.'")');

        if (!empty($this->c_created_user_id)) {
            $query->andWhere('c_created_user_id=' . $this->c_created_user_id);
        } else {
            $query->andWhere(['c_created_user_id' => EmployeeGroupAccess::getUsersIdsInCommonGroups(Auth::id())]);
        }

        if (isset($params['CallSearch']['callDepId']) && $params['CallSearch']['callDepId'] != "") {
            $query->andWhere('c_dep_id= ' . $params['CallSearch']['callDepId']);
        }

        if (!empty($this->c_project_id)) {
            $query->andWhere('c_project_id=' . $this->c_project_id);
        }

        if (!empty($this->userGroupId)) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where('ugs_group_id = ' . $this->userGroupId);
            $query->andWhere(['c_created_user_id' => $userIdsByGroup]);
        }
        $query->groupBy(['c_created_user_id']);

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $model) {
            if (
                $model['outgoingCallsDuration'] == 0 &&
                $model['outgoingCalls'] == 0 &&
                $model['outgoingCallsCompleted'] == 0 &&
                $model['outgoingCallsNoAnswer'] == 0 &&
                $model['outgoingCallsBusy'] == 0 &&
                $model['incomingCallsDuration'] == 0 &&
                $model['incomingCompletedCalls'] == 0 &&
                $model['incomingDirectLine'] == 0 &&
                $model['incomingGeneralLine'] == 0 &&
                $model['redialCallsDuration'] == 0 &&
                $model['totalAttempts'] == 0 &&
                $model['redialCompleted'] == 0
            ) {
                unset($data[$key]);
            }
        }

        $paramsData = [
            'allModels' => $data,
            'sort' => [
                //'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'c_created_user_id',
                    'outgoingCallsDuration',
                    'outgoingCalls',
                    'outgoingCallsCompleted',
                    'outgoingCallsNoAnswer',
                    'outgoingCallsBusy',
                    'incomingCallsDuration',
                    'incomingCompletedCalls',
                    'incomingDirectLine',
                    'incomingGeneralLine',
                    'redialCallsDuration',
                    'totalAttempts',
                    'redialCompleted'
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return $dataProvider = new ArrayDataProvider($paramsData);
    }

    /*
     * @param $params
     * @param $user Employee
     * @return ArrayDataProvider
     * @throws \Exception
     */
    /*public function searchCallsReport($params, $user):ArrayDataProvider
    {
        $this->load($params);
        $timezone = $user->timezone;

        if($this->reportTimezone == null){
            $this->defaultUserTz = $timezone;
        } else {
            $timezone = $this->reportTimezone;
            $this->defaultUserTz = $this->reportTimezone;
        }

        if ($this->timeTo == ""){
            $differenceTimeToFrom  = "24:00";
        } else {
            if((strtotime($this->timeTo) - strtotime($this->timeFrom)) <= 0){
                $differenceTimeToFrom = sprintf("%02d:00",(strtotime("24:00") - strtotime(sprintf("%02d:00", abs((strtotime($this->timeTo) - strtotime($this->timeFrom)) ) / 3600))) / 3600);
            } else {
                $differenceTimeToFrom =  sprintf("%02d:00", (strtotime($this->timeTo) - strtotime($this->timeFrom)) / 3600);
            }
        }

        if ($this->createTimeRange != null) {
            $dates = explode(' - ', $this->createTimeRange);
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

        if (!empty($this->call_duration_from) && empty($this->call_duration_to)) {
            $queryByDuration = ' AND c_call_duration >=' . $this->call_duration_from;
        } elseif (!empty($this->call_duration_to) && empty($this->call_duration_from)) {
            $queryByDuration = ' AND c_call_duration <=' . $this->call_duration_to;
        } elseif (!empty($this->call_duration_from) && !empty($this->call_duration_to)){
            $queryByDuration = ' AND c_call_duration BETWEEN ' . $this->call_duration_from . ' AND '. $this->call_duration_to;
        }else {
            $queryByDuration = '';
        }

        $query = new Query();
        $query->select(['c_created_user_id, DATE(CONVERT_TZ(DATE_SUB(c_created_dt, INTERVAL '.$timeSub.' HOUR), "+00:00", "'. $utcOffsetDST. '")) AS createdDate,

        SUM(IF(c_call_type_id = '. self::CALL_TYPE_OUT .' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> '. self::SOURCE_REDIAL_CALL .' OR c_source_type_id IS NULL), c_call_duration, 0)) AS outgoingCallsDuration,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_OUT .' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> '. self::SOURCE_REDIAL_CALL .' OR c_source_type_id IS NULL), 1, 0)) AS outgoingCalls,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_OUT .' AND c_status_id = '. self::STATUS_COMPLETED .' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> '. self::SOURCE_REDIAL_CALL .' OR c_source_type_id IS NULL) '. $queryByDuration .', 1, 0)) AS outgoingCallsCompleted,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_OUT .' AND c_status_id = '. self::STATUS_NO_ANSWER .' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> '. self::SOURCE_REDIAL_CALL .' OR c_source_type_id IS NULL), 1, 0)) AS outgoingCallsNoAnswer,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_OUT .' AND c_status_id = '. self::STATUS_BUSY.' AND c_parent_call_sid IS NOT NULL AND (c_source_type_id <> '. self::SOURCE_REDIAL_CALL .' OR c_source_type_id IS NULL), 1, 0)) AS outgoingCallsBusy,

        SUM(IF(c_call_type_id = '. self::CALL_TYPE_IN .' AND c_status_id = '. self::STATUS_COMPLETED .' AND c_parent_call_sid IS NOT NULL, c_call_duration, 0)) AS incomingCallsDuration,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_IN .' AND c_status_id = '. self::STATUS_COMPLETED .' AND c_parent_call_sid IS NOT NULL '. $queryByDuration .', 1, 0)) AS incomingCompletedCalls,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_IN .' AND c_status_id = '. self::STATUS_COMPLETED .' AND c_parent_call_sid IS NOT NULL AND c_source_type_id = '. self::SOURCE_DIRECT_CALL .', 1, 0)) AS incomingDirectLine,
        SUM(IF(c_call_type_id = '. self::CALL_TYPE_IN .' AND c_status_id = '. self::STATUS_COMPLETED .' AND c_parent_call_sid IS NOT NULL AND c_source_type_id <> '. self::SOURCE_DIRECT_CALL .', 1, 0)) AS incomingGeneralLine,

        SUM(IF(c_source_type_id = '. self::SOURCE_REDIAL_CALL .' AND c_status_id = '. self::STATUS_COMPLETED .' AND c_parent_call_sid IS NOT NULL , 1, 0)) AS redialCallsDuration,
        SUM(IF(c_source_type_id = '. self::SOURCE_REDIAL_CALL .' AND c_parent_call_sid IS NOT NULL, 1, 0)) AS totalAttempts,
        SUM(IF(c_source_type_id = '. self::SOURCE_REDIAL_CALL .' AND c_status_id = '. self::STATUS_COMPLETED .'  AND c_parent_call_sid IS NOT NULL '. $queryByDuration .', 1, 0)) AS redialCompleted

        ']);
        $query->from('call');
        $query->where('c_created_dt ' .$between_condition);
        $query->andWhere('c_created_user_id IS NOT NULL');
        $query->andWhere('TIME(CONVERT_TZ(DATE_SUB(c_created_dt, INTERVAL '. $timeSub .' HOUR), "+00:00", "'. $utcOffsetDST. '")) <= TIME("'.$differenceTimeToFrom.'")');

        if(!empty($this->c_created_user_id)){
            $query->andWhere('c_created_user_id='. $this->c_created_user_id);
        }

        if (isset($params['CallSearch']['callDepId']) && $params['CallSearch']['callDepId'] != "") {
            $query->andWhere('c_dep_id= ' . $params['CallSearch']['callDepId']);
        }

        if(!empty($this->c_project_id)){
            $query->andWhere('c_project_id='. $this->c_project_id);
        }

        if(!empty($this->userGroupId)){
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where('ugs_group_id = ' . $this->userGroupId);
            $query->andWhere(['c_created_user_id' => $userIdsByGroup]);
        }

        $query->groupBy(['c_created_user_id', 'createdDate']);

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $model){
            if (
                $model['outgoingCallsDuration'] == 0 &&
                $model['outgoingCalls'] == 0 &&
                $model['outgoingCallsCompleted'] == 0 &&
                $model['outgoingCallsNoAnswer'] == 0 &&
                $model['outgoingCallsBusy'] == 0 &&
                $model['incomingCallsDuration'] == 0 &&
                $model['incomingCompletedCalls'] == 0 &&
                $model['incomingDirectLine'] == 0 &&
                $model['incomingGeneralLine'] == 0 &&
                $model['redialCallsDuration'] == 0 &&
                $model['totalAttempts'] == 0 &&
                $model['redialCompleted'] == 0

            ){
                unset($data[$key]);
            }
        }

        $paramsData = [
            'allModels' => $data,
            'sort' => [
                //'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'c_created_user_id',
                    'createdDate',
                    'outgoingCallsDuration',
                    'outgoingCalls',
                    'outgoingCallsCompleted',
                    'outgoingCallsNoAnswer',
                    'outgoingCallsBusy',
                    'incomingCallsDuration',
                    'incomingCompletedCalls',
                    'incomingDirectLine',
                    'incomingGeneralLine',
                    'redialCallsDuration',
                    'totalAttempts',
                    'redialCompleted'
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return $dataProvider = new ArrayDataProvider($paramsData);
    }*/

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchUserCallMapHistory($params): ActiveDataProvider
    {
        $query = Call::find();

        $this->load($params);

        //$query->limit(5);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['c_id' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : [
                'pageSize' => 100,
            ]
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        /*$query->andWhere(['c_call_status' => [Call::CALL_STATUS_RINGING]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_IN_PROGRESS]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_QUEUE]]);*/

        $query->andWhere(['c_parent_id' => null]);

        if ($this->status_ids) {
            $query->andWhere(['c_status_id' => $this->status_ids]);
        }

        if ($this->dep_ids) {
            $query->andWhere(['c_dep_id' => $this->dep_ids]);
        }

        if ($this->project_ids) {
            $query->andWhere(['c_project_id' => $this->project_ids]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                //->join('JOIN', 'user_department', 'ud_user_id = ugs_user_id and ud_dep_id <> :depId', ['depId' => 'ud_dep_id'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->with(['cProject', 'cLead', /*'cLead.leadFlightSegments',*/ 'cCreatedUser', 'cDep', 'callUserAccesses', 'cuaUsers', 'cugUgs', 'calls']);

        return $dataProvider;
    }

    public function searchRealtimeUserCallMapHistory($params)
    {
        $this->load($params);
        $query = Call::find()->alias('c')->limit(10);
        $query->select(['c.c_id', 'c.c_source_type_id', 'c.c_status_id', 'c.c_parent_id', 'c.c_call_type_id', 'c.c_created_user_id', 'c.c_lead_id', 'c.c_case_id',
            'c.c_created_dt', 'c.c_updated_dt', 'c.c_to', 'c.c_from', 'c.c_call_duration', 'c.c_recording_sid',
            'ce.username', 'name as project_name', 'concat(cls.first_name, " ", cls.last_name) as full_name', 'dep_name', 'l.gid', 'cs_gid',
            'group_concat(cau.username SEPARATOR "-") as cua_user_names', 'group_concat(cua_user_id SEPARATOR "-") as cua_user_ids', 'group_concat(cua_status_id SEPARATOR "-") as cua_status_ids'
        ]);
        $query->groupBy(['c.c_id']);
        $query->orderBy(['c.c_id' => SORT_DESC]);


        /*$query->andWhere(['c_call_status' => [Call::CALL_STATUS_RINGING]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_IN_PROGRESS]]);
        $query->orWhere(['c_call_status' => [Call::CALL_STATUS_QUEUE]]);*/

        $query->andWhere(['c.c_parent_id' => null]);

        if ($this->status_ids) {
            $query->andWhere(['c_status_id' => $this->status_ids]);
        }

        if ($this->dep_ids) {
            $query->andWhere(['c_dep_id' => $this->dep_ids]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->joinWith(['cProject', 'cLead as l', 'cCase', 'cClient as cls', 'cCreatedUser as ce', 'cDep', 'callUserAccesses.cuaUser as cau', 'cugUgs']);

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $row) {
            $queryChild = Call::find()->alias('ch');
            $queryChild->select(['ch.c_id', 'ch.c_parent_id', 'ch.c_call_type_id', 'ch.c_created_dt', 'ch.c_updated_dt', 'ch.c_source_type_id', 'ch.c_status_id', 'ch.c_to',
                'ch.c_created_user_id', 'ch.c_call_duration', 'ch.c_recording_sid', 'ce.username', 'dep_name', 'group_concat(cau.username SEPARATOR "-") as cua_user_names',
                'group_concat(cua_user_id SEPARATOR "-") as cua_user_ids', 'group_concat(cua_status_id SEPARATOR "-") as cua_status_ids'
            ]);
            $queryChild->andWhere(['ch.c_parent_id' => $row['c_id']]);
            $queryChild->groupBy(['ch.c_id']);

            $queryChild->joinWith(['cCreatedUser as ce', 'cDep', 'callUserAccesses.cuaUser as cau']);

            $command = $queryChild->createCommand();
            $childData = $command->queryAll();
            foreach ($childData as $row) {
                array_push($data, $row);
            }
        }

        foreach ($data as $key => $row) {
            if ($row['c_created_dt']) {
                $data[$key]['c_created_dt'] = Yii::$app->formatter->asDatetime(strtotime($row['c_created_dt']), 'php: Y-m-d H:i:s');
            }

            if ($row['c_updated_dt']) {
                $data[$key]['c_updated_dt'] = Yii::$app->formatter->asDatetime(strtotime($row['c_updated_dt']), 'php: Y-m-d H:i:s');
            }
        }

        return $data;
    }

    public function getCallHistory($params): ActiveDataProvider
    {
        $this->load($params);

        $query = new Query();

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->limit > 0 ? false : [
                'pageSize' => 10,
            ]
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->select(['c_call_type_id', 'c_from', 'c_to', 'c_caller_name', 'c_created_dt', 'c_status_id', 'c_call_duration']);
        $query->from('call');
        $query->where(['IN', 'c_from', $this->phoneList]);
        $query->orWhere(['IN', 'c_to', $this->phoneList]);
        $query->orderBy(['c_created_dt' => SORT_DESC]);

        //      print_r($query->createCommand()->rawSql);die;

        return $dataProvider;
    }
}
