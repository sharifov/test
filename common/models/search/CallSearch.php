<?php

namespace common\models\search;

use common\models\Department;
use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use sales\repositories\call\CallSearchRepository;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Call;
use common\models\UserGroupAssign;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\Query;

/**
 * CallSearch represents the model behind the search form of `common\models\Call`.
 *
 * @property int $limit
 * @property array $dep_ids
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

    public $call_duration_from;
    public $call_duration_to;
    public $callDepId;
    public $userGroupId;

    public $dep_ids = [];

    private $callSearchRepository;

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
            [['c_id', 'c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_is_new', 'c_is_deleted', 'supervision_id', 'limit', 'c_recording_duration',
                'c_source_type_id', 'call_duration_from', 'call_duration_to', 'c_case_id', 'c_client_id', 'c_status_id', 'callDepId', 'userGroupId'], 'integer'],
            [['c_call_sid', 'c_account_sid', 'c_from', 'c_to', 'c_sip', 'c_call_status', 'c_api_version', 'c_direction', 'c_forwarded_from', 'c_caller_name', 'c_parent_call_sid', 'c_call_duration', 'c_sip_response_code', 'c_recording_url', 'c_recording_sid',
                'c_timestamp', 'c_uri', 'c_sequence_number', 'c_created_dt', 'c_updated_dt', 'c_error_message', 'c_price', 'statuses', 'limit'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['ug_ids', 'status_ids', 'dep_ids'], 'each', 'rule' => ['integer']],
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
            'sort'=> ['defaultOrder' => ['c_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

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

        if ($this->c_created_dt) {
            $query->andFilterWhere(['>=', 'c_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->c_created_dt))])
                ->andFilterWhere(['<=', 'c_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->c_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere(['>=','c_call_duration', $this->call_duration_from]);
        $query->andFilterWhere(['<=','c_call_duration', $this->call_duration_to]);


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
            'c_is_deleted' => $this->c_is_deleted,
            'c_price' => $this->c_price,
            'c_source_type_id' => $this->c_source_type_id,
            'c_call_sid' => $this->c_call_sid,
            'c_parent_call_sid' => $this->c_parent_call_sid,
            'c_client_id' => $this->c_client_id,
            'c_sequence_number' => $this->c_sequence_number,
            'c_recording_sid' => $this->c_recording_sid,
            'c_status_id' => $this->c_status_id

        ]);

        $query
            ->andFilterWhere(['like', 'c_from', $this->c_from])
            ->andFilterWhere(['like', 'c_to', $this->c_to])
            ->andFilterWhere(['like', 'c_call_status', $this->c_call_status])
            ->andFilterWhere(['like', 'c_forwarded_from', $this->c_forwarded_from])
            ->andFilterWhere(['like', 'c_caller_name', $this->c_caller_name])
            ->andFilterWhere(['like', 'c_call_duration', $this->c_call_duration])
            ->andFilterWhere(['like', 'c_recording_url', $this->c_recording_url])
            ->andFilterWhere(['like', 'c_recording_duration', $this->c_recording_duration])
            ->andFilterWhere(['like', 'c_error_message', $this->c_error_message]);

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

        if($this->limit > 0) {
            $query->limit($this->limit);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['c_id' => SORT_DESC]],
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
            'c_is_deleted' => $this->c_is_deleted,
            'c_source_type_id' => $this->c_source_type_id,
            'c_call_sid' => $this->c_call_sid,
            'c_parent_call_sid' => $this->c_parent_call_sid,
            'c_call_status' => $this->c_call_status,
            'c_client_id' => $this->c_client_id,
            'c_status_id' => $this->c_status_id,
            'c_sequence_number' => $this->c_sequence_number
        ]);

        $query
            ->andFilterWhere(['like', 'c_from', $this->c_from])
            ->andFilterWhere(['like', 'c_to', $this->c_to])
            //->andFilterWhere(['like', 'c_call_status', $this->c_call_status])
            ->andFilterWhere(['like', 'c_forwarded_from', $this->c_forwarded_from])
            ->andFilterWhere(['like', 'c_caller_name', $this->c_caller_name])
            ->andFilterWhere(['like', 'c_call_duration', $this->c_call_duration])
            ->andFilterWhere(['like', 'c_recording_url', $this->c_recording_url])
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
        $query = Call::find();

        $this->load($params);

        //$query->limit(5);

        if($this->limit > 0) {
            $query->limit($this->limit);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['c_id' => SORT_DESC]],
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

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                //->join('JOIN', 'user_department', 'ud_user_id = ugs_user_id and ud_dep_id <> :depId', ['depId' => 'ud_dep_id'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->with(['cProject', 'cLead', /*'cLead.leadFlightSegments',*/ 'cCreatedUser', 'cDep', 'callUserAccesses', 'cuaUsers', 'cugUgs', 'calls']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param $user Employee
     * @return SqlDataProvider
     * @throws \Exception
     */
    public function searchCallsReport($params, $user):SqlDataProvider
    {
        $this->load($params);

        $timezone = $user->timezone;
        $userTZ = Employee::timezoneList(false)[$timezone];

        if ($this->createTimeRange != null) {
            $dates = explode(' - ', $this->createTimeRange);
            $hourSub = date('G', strtotime($dates[0]));
            $date_from = Employee::convertTimeFromUserDtToUTC(strtotime($dates[0]));
            $date_to = Employee::convertTimeFromUserDtToUTC(strtotime($dates[1]));
            $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
        } else {
            $hourSub = date('G', strtotime(date('Y-m-d 00:00')));
            $date_from = Employee::convertTimeFromUserDtToUTC(strtotime(date('Y-m-d 00:00')));
            $date_to = Employee::convertTimeFromUserDtToUTC(strtotime(date('Y-m-d 23:59')));
            $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
        }
        $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->userGroupId])->asArray()->all();
        if (isset($params['CallSearch']['c_created_user_id']) && $params['CallSearch']['c_created_user_id'] != "" && empty($this->userGroupId)) {
            $employees = $params['CallSearch']['c_created_user_id'];
        } else if (isset($params['CallSearch']['userGroupId']) && $params['CallSearch']['userGroupId'] != "" && empty($this->c_created_user_id)) {
            $employees = "'" . implode("', '", array_map(function ($entry) {
                    return $entry['ugs_user_id'];
                }, $userIdsByGroup)) . "'";
        } else if (!empty($this->c_created_user_id) && !empty($this->userGroupId)) {
            foreach ($userIdsByGroup as $userIdInGroup) {
                if ($userIdInGroup['ugs_user_id'] == $this->c_created_user_id) {
                    $employees = $userIdInGroup['ugs_user_id'];
                }
            }
        } else {
            $employees = "'" . implode("', '", array_keys(Employee::getList())) . "'";
        }

        if (isset($params['CallSearch']['callDepId']) && $params['CallSearch']['callDepId'] != "") {
            $queryByDepartament = 'AND c_dep_id=' . $params['CallSearch']['callDepId'];
        } else {
            $queryByDepartament = '';
        }

        if(!empty($this->c_project_id)){
            $queryByProject = ' AND c_project_id=' . $this->c_project_id;
        } else {
            $queryByProject = '';
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

        if (!isset($employees)) {
            $employees = 0;
        }
        $query = new Query();

        $query->select(['
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_OUT . ' THEN c_call_duration ELSE 0 END) AS outgoingCallsDuration, 
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_OUT . ' THEN 1 ELSE 0 END) AS outgoingCalls, 
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_OUT . ' AND c_status_id="' . self::STATUS_COMPLETED . '" THEN 1 ELSE 0 END) AS outgoingCallsCompleted, 
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_OUT . ' AND c_status_id="' . self::STATUS_NO_ANSWER . '" THEN 1 ELSE 0 END) AS outgoingCallsNoAnswer, 
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_OUT . ' AND c_status_id="' . self::STATUS_CANCELED . '" THEN 1 ELSE 0 END) AS outgoingCallsCanceled, 
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_IN . ' AND c_status_id="' . self::STATUS_COMPLETED . '" AND c_parent_call_sid IS NOT NULL THEN c_call_duration ELSE 0 END) AS incomingCallsDuration,
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_IN . ' THEN 1 ELSE 0 END) AS incomingCalls,
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_IN . ' AND c_status_id="' . self::STATUS_COMPLETED . '" AND c_parent_call_sid IS NOT NULL THEN 1 ELSE 0 END) AS incomingCompletedCalls,
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_IN . ' AND c_status_id="' . self::STATUS_COMPLETED . '" AND c_parent_call_sid IS NOT NULL AND c_source_type_id=' . self::SOURCE_DIRECT_CALL . ' THEN 1 ELSE 0 END) AS incomingDirectLine,
            SUM(CASE WHEN c_call_type_id=' . self::CALL_TYPE_IN . ' AND c_status_id="' . self::STATUS_COMPLETED . '" AND c_parent_call_sid IS NOT NULL AND c_source_type_id <> ' . self::SOURCE_DIRECT_CALL . ' THEN 1 ELSE 0 END) AS incomingGeneralLine,
            c_created_user_id, DATE(CONVERT_TZ(DATE_SUB(c_created_dt, INTERVAL '.$hourSub.' Hour), "+00:00", "' . $userTZ . '")) AS createdDate 
            FROM `call` WHERE (c_created_dt ' . $between_condition . ') ' . $queryByDepartament . $queryByDuration . $queryByProject . ' AND c_created_user_id in (' . $employees . ')
        ']);

        $query->groupBy(['c_created_user_id, DATE(CONVERT_TZ(DATE_SUB(c_created_dt, INTERVAL '.$hourSub.' Hour), "+00:00", "' . $userTZ . '"))']);
        //$query->orderBy(['c_created_user_id' => SORT_ASC]);

        $command = $query->createCommand();
        $sql = $command->sql;

        $paramsData = [
            'sql' => $sql,
            'sort' => [
                //'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'c_created_user_id' => [
                        'asc' => ['c_created_user_id' => SORT_ASC],
                        'desc' => ['c_created_user_id' => SORT_DESC],
                    ],
                    'createdDate' => [
                        'asc' => ['createdDate' => SORT_ASC],
                        'desc' => ['createdDate' => SORT_DESC],
                    ],
                    'outgoingCallsDuration' => [
                        'asc' => ['outgoingCallsDuration' => SORT_ASC],
                        'desc' => ['outgoingCallsDuration' => SORT_DESC],
                    ],
                    'outgoingCalls' => [
                        'asc' => ['outgoingCalls' => SORT_ASC],
                        'desc' => ['outgoingCalls' => SORT_DESC],
                    ],
                    'outgoingCallsCompleted' => [
                        'asc' => ['outgoingCallsCompleted' => SORT_ASC],
                        'desc' => ['outgoingCallsCompleted' => SORT_DESC],
                    ],
                    'outgoingCallsNoAnswer' => [
                        'asc' => ['outgoingCallsNoAnswer' => SORT_ASC],
                        'desc' => ['outgoingCallsNoAnswer' => SORT_DESC],
                    ],
                    'outgoingCallsCanceled' => [
                        'asc' => ['outgoingCallsCanceled' => SORT_ASC],
                        'desc' => ['outgoingCallsCanceled' => SORT_DESC],
                    ],
                    'incomingCallsDuration' => [
                        'asc' => ['incomingCallsDuration' => SORT_ASC],
                        'desc' => ['incomingCallsDuration' => SORT_DESC],
                    ],
                    /*'incomingCalls' => [
                        'asc' => ['incomingCalls' => SORT_ASC],
                        'desc' => ['incomingCalls' => SORT_DESC],
                    ],*/
                    'incomingCompletedCalls' => [
                        'asc' => ['incomingCompletedCalls' => SORT_ASC],
                        'desc' => ['incomingCompletedCalls' => SORT_DESC],
                    ],
                    'incomingDirectLine' => [
                        'asc' => ['incomingDirectLine' => SORT_ASC],
                        'desc' => ['incomingDirectLine' => SORT_DESC],
                    ],
                    'incomingGeneralLine' => [
                        'asc' => ['incomingGeneralLine' => SORT_ASC],
                        'desc' => ['incomingGeneralLine' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchUserCallMapHistory($params): ActiveDataProvider
    {
        $query = Call::find();

        $this->load($params);

        //$query->limit(5);

        if($this->limit > 0) {
            $query->limit($this->limit);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['c_id' => SORT_DESC]],
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

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])
                //->join('JOIN', 'user_department', 'ud_user_id = ugs_user_id and ud_dep_id <> :depId', ['depId' => 'ud_dep_id'])
                ->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'c_created_user_id', $subQuery]);
        }

        $query->with(['cProject', 'cLead', /*'cLead.leadFlightSegments',*/ 'cCreatedUser', 'cDep', 'callUserAccesses', 'cuaUsers', 'cugUgs', 'calls']);

        return $dataProvider;
    }
}
