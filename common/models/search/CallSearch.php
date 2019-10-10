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
                'c_source_type_id', 'call_duration_from', 'call_duration_to', 'c_case_id', 'c_client_id', 'c_status_id'], 'integer'],
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
