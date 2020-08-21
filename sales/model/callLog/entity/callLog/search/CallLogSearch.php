<?php

namespace sales\model\callLog\entity\callLog\search;

use common\models\Call;
use common\models\Client;
use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use sales\auth\Auth;
use sales\helpers\UserCallIdentity;
use sales\model\callLog\entity\callLog\CallLogCategory;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\callLog\entity\callLog\CallLogType;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogQueue\CallLogQueue;
use sales\model\callNote\entity\CallNote;
use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLog\CallLog;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\VarDumper;

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
    public $createTimeStart;
    public $createTimeEnd;

    public $projectIds = [];
    public $statusIds = [];
    public $typesIds = [];
    public $categoryIds = [];
    public $departmentIds = [];
    public $callDurationFrom;
    public $callDurationTo;
    public $callNote;

    public const CREATE_TIME_START_DEFAULT_RANGE = '-6 days';

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
            [['projectIds', 'statusIds', 'typesIds', 'categoryIds', 'departmentIds'], 'each', 'rule' => ['integer']],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($userTimezone));
        $this->createTimeRange = ($currentDate->modify(self::CREATE_TIME_START_DEFAULT_RANGE))->format('Y-m-d') . ' 00:00:00 - ' . $currentDate->format('Y-m-d') . ' 23:59:59';
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

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()
            ->with(['project', 'department', 'phoneList', 'user', 'record'])
            ->joinWith(['callLogLead.lead', 'callLogCase.case', 'queue']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cl_call_created_dt' => SORT_DESC]],
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
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cl_call_created_dt', $this->cl_call_created_dt, $user->timezone);
        }

        if ($this->cl_call_finished_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cl_call_finished_dt', $this->cl_call_finished_dt, $user->timezone);
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

        if ($this->createTimeRange){
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
            $query->andWhere(['between', 'cl_call_created_dt', $dateTimeStart, $dateTimeEnd]);
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
        ]);

        $query->andFilterWhere(['like', 'cl_call_sid', $this->cl_call_sid])
            ->andFilterWhere(['like', 'cl_phone_from', $this->cl_phone_from])
            ->andFilterWhere(['like', 'cl_phone_to', $this->cl_phone_to]);

        return $dataProvider;
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
            'cl_project_id',
            'cl_department_id',
            'cl_category_id',
            'cl_client_id',
        ]);

        $clientTableName = Client::tableName();
        $query->addSelect([
            "if (" . $clientTableName . ".first_name is not null, if (" . $clientTableName . ".last_name is not null, concat(" . $clientTableName . ".first_name, ' ', " . $clientTableName . ".last_name), " . $clientTableName . ".first_name), null) as client_name",
            'cn_note as callNote'
        ]);

		$clientPrefix  = UserCallIdentity::getFullPrefix();
		$length = strlen($clientPrefix) + 1;
		//todo remove after regexp_substr will be available
        $oldClientPrefix = 'client:seller';
		$lengthOld = strlen($oldClientPrefix) + 1;

//		$query->addSelect([
//		    "IF(
//				call_log.cl_type_id = 1,
//				if (cl_phone_to regexp '" . $clientPrefix . "' = 1, REGEXP_SUBSTR(cl_phone_to, '[[:digit:]]+'), null),
//				if (cl_phone_from regexp '" . $clientPrefix . "' = 1, REGEXP_SUBSTR(cl_phone_from, '[[:digit:]]+'), null)
//            ) AS user_id"
//        ]);
		$query->addSelect([
		    "IF(
				call_log.cl_type_id = 1, 
				if (cl_phone_to regexp '" . $clientPrefix . "' = 1, substring(cl_phone_to from " . $length . "), if (cl_phone_to regexp '" . $oldClientPrefix . "' = 1, substring(cl_phone_to from " . $lengthOld . "),null)), 
				if (cl_phone_from regexp '" . $clientPrefix . "' = 1, substring(cl_phone_from from " . $length . "), if (cl_phone_from regexp '" . $oldClientPrefix . "' = 1, substring(cl_phone_from from " . $lengthOld . "),null))
            ) AS user_id"
        ]);
		$query->leftJoin($clientTableName, $clientTableName . '.id = cl_client_id');
		$query->leftJoin( CallLogLead::tableName(),  'cll_cl_id = cl_id');
		$query->leftJoin( CallLogCase::tableName(),  'clc_cl_id = cl_id');
		$query->leftJoin(CallNote::tableName(), new Expression('cn_id = (select cn_id from call_note where cn_call_id = cl_id order by cn_created_dt desc limit 1)'));
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
            'cl_project_id',
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
            Employee::tableName() . '.nickname as nickname',
            new Expression('if (client_name is not null, client_name, if (cl_type_id = 1, if(' . $userTableName . '.nickname is not null, ' . $userTableName . '.nickname, cl_phone_to), if(' . $userTableName . '.nickname is not null, ' . $userTableName . '.nickname, cl_phone_from))) as formatted'),
        ])
            ->from($query)
            ->leftJoin(Employee::tableName(), Employee::tableName() . '.id = user_id');

//		VarDumper::dump($q->createCommand()->getRawSql());die;

		return $dataProvider;
	}

    public function searchMyCalls($params, Employee $user): ActiveDataProvider
    {
        $this->load($params);

        $query = static::find();
        $query->where(['cl_user_id' => $user->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cl_id' => SORT_DESC]],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->cl_call_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cl_call_created_dt', $this->cl_call_created_dt, $user->timezone);
        }

        if ($this->createTimeRange){
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
            $query->andWhere(['between', 'cl_call_created_dt', $dateTimeStart, $dateTimeEnd]);
        }

        if ($this->projectIds){
            $query->andWhere(['cl_project_id' => $this->projectIds]);
        }

        if ($this->statusIds){
            $query->andWhere(['cl_status_id' => $this->statusIds]);
        }

        if ($this->typesIds){
            $query->andWhere(['cl_type_id' => $this->typesIds]);
        }
        if ($this->categoryIds){
            $query->andWhere(['cl_category_id' => $this->categoryIds]);
        }

        if ($this->departmentIds){
            $query->andWhere(['cl_department_id' => $this->departmentIds]);
        }

        if($this->callDurationFrom){
            $query->andWhere(['>=', 'cl_duration', $this->callDurationFrom]);
        }

        if ($this->callDurationTo){
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
        ]);

        $query->andFilterWhere(['like', 'cl_phone_from', $this->cl_phone_from])
            ->andFilterWhere(['like', 'cl_phone_to', $this->cl_phone_to]);

        return $dataProvider;
    }
}
