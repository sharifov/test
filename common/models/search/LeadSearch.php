<?php

namespace common\models\search;

use common\components\ChartTools;
use common\models\Airports;
use common\models\Call;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\ProfitSplit;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\Sms;
use common\models\Sources;
use common\models\TipsSplit;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use common\models\UserProfile;
use modules\fileStorage\src\entity\fileLead\FileLead;
use src\access\EmployeeGroupAccess;
use src\access\EmployeeProjectAccess;
use src\auth\Auth;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\leadData\entity\LeadData;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\leadUserRating\entity\LeadUserRating;
use src\model\leadUserRating\entity\LeadUserRatingQuery;
use src\model\quoteLabel\entity\QuoteLabel;
use src\repositories\lead\LeadBadgesRepository;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * LeadSearch represents the model behind the search form of `common\models\Lead`.
 *
 * @property $remainingDays
 * @property $grade
 * @property $inCalls
 * @property $inCallsDuration
 * @property $outCalls
 * @property $outCallsDuration
 * @property $smsOffers
 * @property $emailOffers
 * @property $quoteType
 * @property int $l_is_test
 *
 * @property int|null $emailsQtyFrom
 * @property int|null $emailsQtyTo
 * @property int|null $smsQtyFrom
 * @property int|null $smsQtyTo
 * @property int|null $callsQtyFrom
 * @property int|null $callsQtyTo
 * @property int|null $chatsQtyFrom
 * @property int|null $chatsQtyTo
 * @property array $show_fields
 * @property int|null $quoteTypeId
 * @property string|null $expiration_dt
 * @property integer|null $lead_type
 * @property string|null $lead_data_key
 * @property string|null $lead_data_value
 * @property array|null $quote_labels
 * @property bool|null $is_conversion
 *
 * @property $count_files
 * @property int|null $includedFiles
 */
class LeadSearch extends Lead
{
    public $client_name;
    public $client_email;
    public $client_phone;
    public $quote_pnr;
    public $cnt;

    public $statuses = [];
    public $created_date_from;
    public $created_date_to;
    public $depart_date_from;
    public $depart_date_to;
    public $sold_date_from;
    public $sold_date_to;
    public $processing_filter;

    public $supervision_id;

    /* processing search form */
    public $email_status;
    public $quote_status;

    public $limit;

    public $origin_airport;
    public $destination_airport;
    public $origin_country;
    public $destination_country;

    public $datetime_start;
    public $datetime_end;
    public $date_range;

    public $departRangeTime;
    public $createdRangeTime;
    public $updatedRangeTime;
    public $lastActionRangeTime;
    public $statusRangeTime;
    public $soldRangeTime;
    public $createTimeRange;
    public $createdType;
    public $leadType;
    public $reportTimezone;
    public $timeFrom;
    public $timeTo;
    public $defaultUserTz;

    public $last_ticket_date;

    public $remainingDays;

    public $grade;
    public $inCalls;
    public $inCallsDuration;
    public $outCalls;
    public $outCallsDuration;
    public $smsOffers;
    public $emailOffers;
    public $quoteType;

    public $l_is_test;

    public $lfOwnerId;
    public $userGroupId;
    public $departmentId;
    public $projectId;

    public $emailsQtyFrom;
    public $emailsQtyTo;
    public $smsQtyFrom;
    public $smsQtyTo;
    public $callsQtyFrom;
    public $callsQtyTo;
    public $chatsQtyFrom;
    public $chatsQtyTo;
    public $count_files;
    public $includedFiles;
    public $show_fields = [];
    public $quoteTypeId;
    public $expiration_dt;
    public $lead_type;
    public $lead_data_key;
    public $lead_data_value;
    public $quote_labels;
    public $is_conversion;
    public $lead_user_rating;
    public $extra_timer;
    public $excludeExtraQueue;
    public $excludeBonusQueue;
    private $leadBadgesRepository;

    private $defaultDateRange;
    private $defaultMinDate;
    private $defaultMaxDate;

    public function __construct($config = [])
    {
        $this->leadBadgesRepository = new LeadBadgesRepository();
        $this->defaultMinDate = date("Y-m-01 00:00");
        $this->defaultMaxDate = date("Y-m-d 23:59");
        $this->defaultDateRange = $this->defaultMinDate . ' - ' . $this->defaultMaxDate;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end', 'createTimeRange'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['id', 'client_id', 'employee_id', 'status', 'project_id', 'projectId', 'adults', 'children', 'infants', 'rating', 'called_expert', 'cnt', 'l_answered', 'supervision_id', 'limit', 'bo_flight_id', 'l_duplicate_lead_id', 'l_type_create'], 'integer'],
            [['email_status', 'quote_status', 'l_is_test', 'l_type'], 'integer'],
            [['lfOwnerId', 'userGroupId', 'departmentId', 'projectId', 'createdType', 'lead_type'], 'integer'],

            [['client_name', 'client_email', 'quote_pnr', 'gid', 'origin_country', 'destination_country', 'l_request_hash'], 'string'],

            [['origin_airport', 'destination_airport'], 'safe'],

            //['created_date_from', 'default', 'value' => '2018-01-01'],
            //['created_date_to', 'default', 'value' => date('Y-m-d')],

            [['uid', 'hybrid_uid', 'trip_type', 'cabin', 'notes_for_experts', 'created', 'updated', 'request_ip', 'request_ip_detail', 'offset_gmt', 'snooze_for', 'discount_id',
                'created_date_from', 'created_date_to', 'depart_date_from', 'depart_date_to', 'source_id', 'statuses', 'sold_date_from', 'sold_date_to', 'processing_filter', 'l_init_price', 'l_last_action_dt'], 'safe'],
            ['l_init_price', 'filter', 'filter' => static function ($value) {
                return $value ? filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
            }],

            [['created', 'updated', 'l_last_action_dt', 'l_status_dt'], 'date', 'format' => 'php:Y-m-d'],

            [['last_ticket_date', 'show_fields'], 'safe'],
            // [['show_fields'],'checkIsArray'],

            ['show_fields', 'filter', 'filter' => static function ($value) {
                return is_array($value) ? $value : [];
            }, 'skipOnEmpty' => true],

            [['departRangeTime', 'createdRangeTime', 'soldRangeTime', 'updatedRangeTime', 'lastActionRangeTime', 'statusRangeTime'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],

            ['remainingDays', 'integer'],
            ['remainingDays', 'filter', 'filter' => static function ($value) {
                return (int)$value;
            }, 'skipOnEmpty' => true],

            ['l_is_test', 'in', 'range' => [0,1]],
            ['l_call_status_id', 'integer'],
            [['defaultUserTz', 'reportTimezone', 'timeFrom', 'timeTo'], 'string'],
            [['quote_pnr'], 'string', 'min' => 5],
            [
                [
                    'emailsQtyFrom', 'emailsQtyTo', 'smsQtyFrom', 'smsQtyTo',
                    'callsQtyFrom', 'callsQtyTo', 'chatsQtyFrom', 'chatsQtyTo',
                ],
                'integer', 'min' => 0, 'max' => 1000
            ],

            ['client_phone', 'filter', 'filter' => static function ($value) {
                $value = preg_replace('~[^0-9\+]~', '', $value);
                $value = (strpos($value, '+') === 0 ? '+' : '') . str_replace('+', '', $value);
                return (string) $value;
            }, 'skipOnEmpty' => true],
            [['client_phone'], 'string', 'max' => 20],

            ['quoteTypeId', 'integer'],
            ['quoteTypeId', 'in', 'range' => array_keys(Quote::TYPE_LIST)],

            ['includedFiles', 'in', 'range' => [0, 1]],

            [['expiration_dt'], 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true],

            [['lead_data_key', 'lead_data_value'], 'string', 'max' => 50],
            [['lead_data_key', 'lead_data_value'], 'trim'],

            [['quote_labels'], 'safe'],

            [['is_conversion'], 'in', 'range' => [0, 1]],

            ['sold_date_from', 'default', 'value' => $this->defaultMinDate],
            ['sold_date_to', 'default', 'value' => $this->defaultMaxDate],
            ['createTimeRange', 'default', 'value' => $this->defaultDateRange],
            ['lead_user_rating', 'in', 'range' => array_keys(LeadUserRating::getRatingList())],
            [['extra_timer'],'safe'],
            [['excludeExtraQueue', 'excludeBonusQueue'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        $labels2 = [
            'emailsQtyFrom' => 'Emails From', 'emailsQtyTo' => 'Emails To',
            'smsQtyFrom' => 'Sms From', 'smsQtyTo' => 'Sms To',
            'callsQtyFrom' => 'Calls From', 'callsQtyTo' => 'Calls To',
            'chatsQtyFrom' => 'Chats From', 'chatsQtyTo' => 'Chats To',
            'projectId' => 'Project',
            'quoteTypeId' => 'Quote Type',
            'includedFiles' => 'Included Files',
            'origin_airport' => 'Origin Location Code',
            'destination_airport' => 'Destination Location Code',
            'lead_data_key' => 'Data Key',
            'lead_data_value' => 'Data Value',
            'quote_labels' => 'Quote labels',
            'is_conversion' => 'Is Conversion',
        ];
        return array_merge(parent::attributeLabels(), $labels2);
    }

    /**
     * @return string[]
     */
    public function getViewFields(): array
    {
        $data = [
//            'uid' => 'UID',
//            'gid' => 'GID',
//            'client_id' => 'Client ID',
//            'employee_id' => 'Employee ID',
//            'status' => 'Status',
//            'project_id' => 'Project',
//            'source_id' => 'Source ID',
//            'trip_type' => 'Trip Type',
//            'cabin' => 'Cabin',
//            'adults' => 'Adults',
//            'children' => 'Children',
//            'infants' => 'Infants',
//            'notes_for_experts' => 'Notes for Expert',
//            'created' => 'Created',
//            'updated' => 'Updated',
//            'l_answered' => 'Answered',
//            'bo_flight_id' => '(BO) Flight ID',
//            'agents_processing_fee' => 'Agents Processing Fee',
//            'origin_country' => 'Origin Country code',
//            'destination_country' => 'Destination Country code',
//            'l_call_status_id' => 'Call status',
//            'l_pending_delay_dt' => 'Pending delay',
//
//            'l_client_first_name' => 'Client First Name',
//            'l_client_last_name' => 'Client Last Name',
//            'l_client_phone' => 'Client Phone',
//            'l_client_email' => 'Client Email',
//            'l_client_lang' => 'Client Lang',
//            'l_client_ua' => 'Client UserAgent',
//            'l_request_hash' => 'Request Hash',
//            'l_duplicate_lead_id' => 'Duplicate Lead ID',
//
//            'l_init_price' => 'Init Price',

//            'l_dep_id' => 'Department ID',
//            'l_delayed_charge' => 'Delayed charge',
//
//            'l_visitor_log_id' => 'Visitor log ID',


            'quotes' => 'Quotes',
            'pnr' => 'PNR',
            'updated' => 'Updated',
            'depart' => 'Depart',
            'segments' => 'Segments',
            'expert_quotes' => 'Expert Quotes',
            'hybrid_uid' => 'Booking ID',
            'communication' => 'Communication',
            'status_flow' => 'Status flow',
            'l_last_action_dt' => 'Last Action',
            'check_list' => 'Check List',
            'count_files' => 'Files',
            'expiration_dt' => 'Expiration',
            'l_type' => 'Lead Type',
        ];
        return $data;
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, Employee $user)
    {
        $query = static::find()->with('project', 'lDep', 'source', 'employee', 'client', 'leadFlows');
        $query->select([
            Lead::tableName() . '.*',
            'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")
        ]);

        if (!$user->isOnlyAdmin() && !$user->isSuperAdmin()) {
            /*$query->andWhere([
                Lead::tableName() . '.project_id' => ProjectEmployeeAccess::find()
                    ->select(ProjectEmployeeAccess::tableName() . '.project_id')
                    ->andWhere([ProjectEmployeeAccess::tableName() . '.employee_id' => $user->id])
            ]);*/
            $query->andWhere([
                Lead::tableName() . '.project_id' => ProjectEmployeeAccess::find()
                    ->select(ProjectEmployeeAccess::tableName() . '.project_id')
                    ->andWhere([ProjectEmployeeAccess::tableName() . '.employee_id' => $user->id])->asArray()->column()
            ]);
            /*$query->andWhere([
                Lead::tableName() . '.l_dep_id' => UserDepartment::find()
                    ->select(UserDepartment::tableName() . '.ud_dep_id')
                    ->andWhere([UserDepartment::tableName() . '.ud_user_id' => $user->id])
            ]);*/
            $query->andWhere([
                Lead::tableName() . '.l_dep_id' => UserDepartment::find()
                    ->select(UserDepartment::tableName() . '.ud_dep_id')
                    ->andWhere([UserDepartment::tableName() . '.ud_user_id' => $user->id])->asArray()->column()
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['leads.id' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            'leads.id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ]
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->projectId) {
            $query->andWhere(['project_id' => $this->projectId]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Lead::tableName() . '.id' => $this->id,
            'gid'   => $this->gid,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,
            'called_expert' => $this->called_expert,
            'l_answered'    => $this->l_answered,
            'l_duplicate_lead_id' => $this->l_duplicate_lead_id,
            'l_init_price'  => $this->l_init_price,
            'request_ip'    => $this->request_ip,
            'l_is_test'     => $this->l_is_test,
            'hybrid_uid' => $this->hybrid_uid,
            'l_type' => $this->l_type,
        ]);

        if ($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }

        if ($this->createdRangeTime) {
            $createdRange = explode(" - ", $this->createdRangeTime);
            if ($createdRange[0]) {
                $query->andFilterWhere(['>=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[0]))]);
            }
            if ($createdRange[1]) {
                $query->andFilterWhere(['<=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[1]))]);
            }
        }

        if ($this->updatedRangeTime) {
            $updatedRange = explode(" - ", $this->updatedRangeTime);
            if ($updatedRange[0]) {
                $query->andFilterWhere(['>=', 'leads.updated', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[0]))]);
            }
            if ($updatedRange[1]) {
                $query->andFilterWhere(['<=', 'leads.updated', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[1]))]);
            }
        }

        if ($this->lastActionRangeTime) {
            $lastActionRange = explode(" - ", $this->lastActionRangeTime);
            if ($lastActionRange[0]) {
                $query->andFilterWhere(['>=', 'leads.l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastActionRange[0]))]);
            }
            if ($lastActionRange[1]) {
                $query->andFilterWhere(['<=', 'leads.l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastActionRange[1]))]);
            }
        }
        if ($this->statusRangeTime) {
            $statusRange = explode(" - ", $this->statusRangeTime);
            if ($statusRange[0]) {
                $query->andFilterWhere(['>=', 'leads.l_status_dt', Employee::convertTimeFromUserDtToUTC(strtotime($statusRange[0]))]);
            }
            if ($statusRange[1]) {
                $query->andFilterWhere(['<=', 'leads.l_status_dt', Employee::convertTimeFromUserDtToUTC(strtotime($statusRange[1]))]);
            }
        }

        if ($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '" . date('Y-m-d', strtotime($departRange[0])) . "'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '" . date('Y-m-d', strtotime($departRange[1])) . "'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->soldRangeTime) {
            $soldRange = explode(" - ", $this->soldRangeTime);
            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($soldRange[0]) {
                $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($soldRange[0]))]);
            }
            if ($soldRange[1]) {
                $subQuery->andFilterWhere(['<=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($soldRange[1]))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if ($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if ($this->client_phone) {
            $this->client_phone = (strpos($this->client_phone, '+') === 0 ? '+' : '') . str_replace('+', '', $this->client_phone);
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone])->asArray()->column();
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if ($this->quote_pnr) {
            //$subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            //$query->andWhere(['IN', 'leads.id', $subQuery]);

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"' . $this->quote_pnr . '"%\'')]);
        }

        if ($this->excludeBonusQueue) {
            $query->andFilterWhere(['<>', 'leads.status', Lead::STATUS_FOLLOW_UP]);
        }

        if ($this->excludeExtraQueue) {
            $query->andFilterWhere(['<>', 'leads.status', Lead::STATUS_EXTRA_QUEUE]);
        }

        if ($this->supervision_id > 0) {
            if (
                $this->id
                || $this->uid
                || $this->client_id
                || $this->client_email
                || $this->client_phone
                || $this->status == Lead::STATUS_FOLLOW_UP
                || $this->status == Lead::STATUS_PENDING
                || $this->request_ip
                || $this->discount_id
                || $this->gid
                || $this->bo_flight_id
            ) {
            } else {
                if ($this->statuses && in_array(Lead::STATUS_FOLLOW_UP, $this->statuses) && count($this->statuses) == 1) {
                } elseif ($this->statuses && in_array(Lead::STATUS_PENDING, $this->statuses) && count($this->statuses) == 1) {
                } else {
                    $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
                    $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
                    $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
                }
            }
        }

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'notes_for_experts', $this->notes_for_experts])
            //->andFilterWhere(['like', 'request_ip', $this->request_ip])
            ->andFilterWhere(['like', 'request_ip_detail', $this->request_ip_detail])
            ->andFilterWhere(['like', 'offset_gmt', $this->offset_gmt])
            ->andFilterWhere(['like', 'discount_id', $this->discount_id]);

        if (!empty($this->origin_airport)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if (!empty($this->destination_airport)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if (!empty($this->origin_country)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.a_country_code',$this->origin_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.a_country_code',$this->origin_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }
        if (!empty($this->destination_country)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.a_country_code',$this->destination_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.a_country_code',$this->destination_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if ($this->createdType) {
            $query->andWhere(['l_type_create' => $this->createdType]);
        }

        if ($this->lead_type) {
            $query->andWhere(['l_type' => $this->lead_type]);
        }

        if ($this->expiration_dt) {
            $query->andWhere(new Expression(
                'DATE(l_expiration_dt) = :date',
                [':date' => date('Y-m-d', strtotime($this->expiration_dt))]
            ));
        }

        if ($this->quoteTypeId) {
             $query->andWhere([
                'IN',
                'leads.id',
                Quote::find()
                    ->select(['DISTINCT(lead_id)'])
                    ->where(['type_id' => $this->quoteTypeId])
                    ->groupBy('lead_id')
             ]);
        }

        if ($this->lead_data_key) {
             $query->andWhere([
                'IN',
                'leads.id',
                LeadData::find()
                    ->select(['DISTINCT(ld_lead_id)'])
                    ->where(['ld_field_key' => $this->lead_data_key])
                    ->groupBy('ld_lead_id')
             ]);
        }

        if ($this->lead_data_value !== '') {
             $query->andWhere([
                'IN',
                'leads.id',
                LeadData::find()
                    ->select(['DISTINCT(ld_lead_id)'])
                    ->where(['ld_field_value' => $this->lead_data_value])
                    ->groupBy('ld_lead_id')
             ]);
        }

        if (ArrayHelper::isIn($this->includedFiles, ['1', '0'], false)) {
            $leadIds = FileLead::find()
                ->select('fld_lead_id')
                ->groupBy(['fld_lead_id'])
                ->indexBy('fld_lead_id')
                ->column();
            $command = $this->includedFiles ? 'IN' : 'NOT IN';

            $query->andWhere([
                $command,
                'leads.id',
                $leadIds
            ]);
        }

        if (!empty($this->emailsQtyFrom) || !empty($this->emailsQtyTo)) {
            $query->leftJoin([
                'emails' => Email::find()
                    ->select([
                        'e_lead_id',
                        new Expression('COUNT(e_lead_id) AS cnt')
                    ])
                    ->groupBy(['e_lead_id'])
            ], 'leads.id = emails.e_lead_id');

            if (!empty($this->emailsQtyFrom)) {
                if ((int) $this->emailsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'emails.cnt', $this->emailsQtyFrom],
                            ['IS', 'emails.e_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'emails.cnt', $this->emailsQtyFrom]);
                }
            }
            if (!empty($this->emailsQtyTo)) {
                if ((int) $this->emailsQtyTo === 0 || (int) $this->emailsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'emails.cnt', $this->emailsQtyTo],
                            ['IS', 'emails.e_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'emails.cnt', $this->emailsQtyTo]);
                }
            }
        }

        if (!empty($this->smsQtyFrom) || !empty($this->smsQtyTo)) {
            $query->leftJoin([
                'sms' => Sms::find()
                    ->select([
                        's_lead_id',
                        new Expression('COUNT(s_lead_id) AS cnt')
                    ])
                    ->groupBy(['s_lead_id'])
            ], 'leads.id = sms.s_lead_id');

            if (!empty($this->smsQtyFrom)) {
                if ((int) $this->smsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'sms.cnt', $this->smsQtyFrom],
                            ['IS', 'sms.s_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'sms.cnt', $this->smsQtyFrom]);
                }
            }
            if (!empty($this->smsQtyTo)) {
                if ((int) $this->smsQtyTo === 0 || (int) $this->smsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'sms.cnt', $this->smsQtyTo],
                            ['IS', 'sms.s_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'sms.cnt', $this->smsQtyTo]);
                }
            }
        }

        if (!empty($this->chatsQtyFrom) || !empty($this->chatsQtyTo)) {
            $query->leftJoin([
                'chats' => ClientChatLead::find()
                    ->select([
                        'ccl_lead_id',
                        new Expression('COUNT(ccl_lead_id) AS cnt')
                    ])
                    ->groupBy(['ccl_lead_id'])
            ], 'leads.id = chats.ccl_lead_id');

            if (!empty($this->chatsQtyFrom)) {
                if ((int) $this->chatsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'chats.cnt', $this->chatsQtyFrom],
                            ['IS', 'chats.ccl_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'chats.cnt', $this->chatsQtyFrom]);
                }
            }
            if (!empty($this->chatsQtyTo)) {
                if ((int) $this->chatsQtyTo === 0 || (int) $this->chatsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'chats.cnt', $this->chatsQtyTo],
                            ['IS', 'chats.ccl_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'chats.cnt', $this->chatsQtyTo]);
                }
            }
        }

        if (!empty($this->callsQtyFrom) || !empty($this->callsQtyTo)) {
            $query->leftJoin([
                'calls' => CallLogLead::find()
                    ->select([
                        'cll_lead_id AS c_lead_id',
                        new Expression('COUNT(cll_lead_id) AS cnt')
                    ])
                    ->innerJoin(
                        CallLog::tableName(),
                        CallLog::tableName() . '.cl_id = ' . CallLogLead::tableName() . '.cll_cl_id'
                    )
                    ->where(['IN', 'cl_type_id', [CallLogType::IN, CallLogType::OUT]])
                    ->groupBy(['cll_lead_id'])
            ], Lead::tableName() . '.id = calls.c_lead_id');
            if (!empty($this->callsQtyFrom)) {
                if ((int) $this->callsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'calls.cnt', $this->callsQtyFrom],
                            ['IS', 'calls.c_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'calls.cnt', $this->callsQtyFrom]);
                }
            }
            if (!empty($this->callsQtyTo)) {
                if ((int) $this->callsQtyTo === 0 || (int) $this->callsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'calls.cnt', $this->callsQtyTo],
                            ['IS', 'calls.c_lead_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'calls.cnt', $this->callsQtyTo]);
                }
            }
        }

        if ($this->quote_labels) {
            $quoteSubQuery = Quote::find()
                ->select('lead_id')
                ->innerJoin([
                    'quote_label' => QuoteLabel::find()
                        ->select(['ql_quote_id'])
                        ->where(['IN', 'ql_label_key', $this->quote_labels])
                        ->having(['>=', new Expression('COUNT(' . QuoteLabel::tableName() . '.ql_quote_id)'), count($this->quote_labels)])
                        ->groupBy(['ql_quote_id'])
                ], Quote::tableName() . '.id = quote_label.ql_quote_id')
                ->groupBy(['lead_id']);

            $query->innerJoin([
                'quote_label' => $quoteSubQuery
            ], 'leads.id = quote_label.lead_id');
        }

        if (ArrayHelper::isIn($this->is_conversion, ['1', '0'], false)) {
            $leadIds = LeadUserConversion::find()
                ->select('luc_lead_id')
                ->groupBy(['luc_lead_id']);

            $command = $this->is_conversion ? 'IN' : 'NOT IN';

            $query->andWhere([
                $command,
                'leads.id',
                $leadIds
            ]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchExport($params): ActiveDataProvider
    {
        $query = Lead::find()->with('project', 'source', 'employee', 'client');
        $query->select(['id', 'uid', 'l_type_create', 'status', 'client_id', 'called_expert', 'project_id', 'source_id', 'trip_type', 'cabin', 'adults', 'children', 'infants', 'employee_id', 'created', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        if (isset($params['LeadSearch']) && !array_filter($params['LeadSearch']) || empty($params)) {
            $query->where('0=1');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['leads.id' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            'leads.id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ]
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'gid'   => $this->gid,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,
            'called_expert' => $this->called_expert,
            'l_answered'    => $this->l_answered,
            'l_duplicate_lead_id' => $this->l_duplicate_lead_id,
            'l_init_price'  => $this->l_init_price,
            'request_ip'    => $this->request_ip,
            'l_type_create' => $this->l_type_create
        ]);

        if ($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }

        if ($this->createdType) {
            $query->andWhere(['l_type_create' => $this->createdType]);
        }

        if ($this->createdRangeTime) {
            $createdRange = explode(" - ", $this->createdRangeTime);
            if ($createdRange[0]) {
                $query->andFilterWhere(['>=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[0]))]);
            }
            if ($createdRange[1]) {
                $query->andFilterWhere(['<=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[1]))]);
            }
        }

        if ($this->updatedRangeTime) {
            $updatedRange = explode(" - ", $this->updatedRangeTime);
            if ($updatedRange[0]) {
                $query->andFilterWhere(['>=', 'leads.updated', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[0]))]);
            }
            if ($updatedRange[1]) {
                $query->andFilterWhere(['<=', 'leads.updated', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[1]))]);
            }
        }

        if ($this->lastActionRangeTime) {
            $lastActionRange = explode(" - ", $this->lastActionRangeTime);
            if ($lastActionRange[0]) {
                $query->andFilterWhere(['>=', 'leads.l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastActionRange[0]))]);
            }
            if ($lastActionRange[1]) {
                $query->andFilterWhere(['<=', 'leads.l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastActionRange[1]))]);
            }
        }

        if ($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '" . date('Y-m-d', strtotime($departRange[0])) . "'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '" . date('Y-m-d', strtotime($departRange[1])) . "'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->soldRangeTime) {
            $soldRange = explode(" - ", $this->soldRangeTime);
            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($soldRange[0]) {
                $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($soldRange[0]))]);
            }
            if ($soldRange[1]) {
                $subQuery->andFilterWhere(['<=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($soldRange[1]))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if ($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if ($this->client_phone) {
            $this->client_phone = (strpos($this->client_phone, '+') === 0 ? '+' : '') . str_replace('+', '', $this->client_phone);
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if ($this->quote_pnr) {
            //$subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            //$query->andWhere(['IN', 'leads.id', $subQuery]);

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"' . $this->quote_pnr . '"%\'')]);
        }

        if ($this->supervision_id > 0) {
            if (
                $this->id
                || $this->uid
                || $this->client_id
                || $this->client_email
                || $this->client_phone
                || $this->status == Lead::STATUS_FOLLOW_UP
                || $this->request_ip
                || $this->discount_id
                || $this->gid
                || $this->bo_flight_id
            ) {
            } else {
                if ($this->statuses && in_array(Lead::STATUS_FOLLOW_UP, $this->statuses) && count($this->statuses) == 1) {
                } elseif ($this->statuses && in_array(Lead::STATUS_PENDING, $this->statuses) && count($this->statuses) == 1) {
                } else {
                    $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
                    $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
                    $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
                }
            }
        }

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'notes_for_experts', $this->notes_for_experts])
            //->andFilterWhere(['like', 'request_ip', $this->request_ip])
            ->andFilterWhere(['like', 'request_ip_detail', $this->request_ip_detail])
            ->andFilterWhere(['like', 'offset_gmt', $this->offset_gmt])
            ->andFilterWhere(['like', 'discount_id', $this->discount_id]);

        if (!empty($this->origin_airport)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if (!empty($this->destination_airport)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if (!empty($this->origin_country)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.a_country_code',$this->origin_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.a_country_code',$this->origin_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }
        if (!empty($this->destination_country)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.a_country_code',$this->destination_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.a_country_code',$this->destination_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        $query->addSelect([
            'grade' => (new Query())
                ->select(['count(*)'])
                ->from(LeadFlow::tableName())
                ->andWhere(LeadFlow::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(LeadFlow::tableName() . '.status = ' . Lead::STATUS_FOLLOW_UP)
        ]);

        $query->addSelect([
            'inCalls' => (new Query())
                ->select(['count(*)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_IN])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'inCallsDuration' => (new Query())
                ->select(['sum(' . Call::tableName() . '.c_recording_duration)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_IN])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'outCalls' => (new Query())
                ->select(['count(*)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_OUT])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'outCallsDuration' => (new Query())
                ->select(['sum(' . Call::tableName() . '.c_recording_duration)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_OUT])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'smsOffers' => (new Query())
                ->select(['count(*)'])
                ->from(Sms::tableName())
                ->andWhere([Sms::tableName() . '.s_template_type_id' => 2])
                ->andWhere(Sms::tableName() . '.s_lead_id = ' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'emailOffers' => (new Query())
                ->select(['count(*)'])
                ->from(Email::tableName())
                ->andWhere([Email::tableName() . '.e_template_type_id' => 1])
                ->andWhere(Email::tableName() . '.e_lead_id = ' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'quoteType' => (new Query())
                ->select([Quote::tableName() . '.created_by_seller'])
                ->from(Quote::tableName())
                ->andWhere([Quote::tableName() . '.status' => Quote::STATUS_APPLIED])
                ->andWhere(Quote::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->limit(1)
        ]);

//        $sqlRaw = $query->createCommand()->getRawSql();
//        VarDumper::dump($sqlRaw, 10, true); die;

        return $dataProvider;
    }

    public function searchExportCsv($params, $offset, $limit)
    {
        $query = Lead::find()->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->asArray();
        $query->select(['id', 'uid', 'l_type_create', 'status', 'client_id', 'called_expert', 'project_id', 'source_id', 'trip_type', 'cabin', 'adults', 'children', 'infants', 'employee_id', 'createdDate' => new Expression("DATE(created)"), 'createdTime' => new Expression("TIME(created)"), 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'gid'   => $this->gid,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,
            'called_expert' => $this->called_expert,
            'l_answered'    => $this->l_answered,
            'l_duplicate_lead_id' => $this->l_duplicate_lead_id,
            'l_init_price'  => $this->l_init_price,
            'request_ip'    => $this->request_ip,
            'l_type_create' => $this->l_type_create
        ]);

        if ($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }

        if ($this->createdType) {
            $query->andWhere(['l_type_create' => $this->createdType]);
        }

        if ($this->createdRangeTime) {
            $createdRange = explode(" - ", $this->createdRangeTime);
            if ($createdRange[0]) {
                $query->andFilterWhere(['>=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[0]))]);
            }
            if ($createdRange[1]) {
                $query->andFilterWhere(['<=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[1]))]);
            }
        }

        if ($this->updatedRangeTime) {
            $updatedRange = explode(" - ", $this->updatedRangeTime);
            if ($updatedRange[0]) {
                $query->andFilterWhere(['>=', 'leads.updated', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[0]))]);
            }
            if ($updatedRange[1]) {
                $query->andFilterWhere(['<=', 'leads.updated', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[1]))]);
            }
        }

        if ($this->lastActionRangeTime) {
            $lastActionRange = explode(" - ", $this->lastActionRangeTime);
            if ($lastActionRange[0]) {
                $query->andFilterWhere(['>=', 'leads.l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastActionRange[0]))]);
            }
            if ($lastActionRange[1]) {
                $query->andFilterWhere(['<=', 'leads.l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastActionRange[1]))]);
            }
        }

        if ($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '" . date('Y-m-d', strtotime($departRange[0])) . "'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '" . date('Y-m-d', strtotime($departRange[1])) . "'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->soldRangeTime) {
            $soldRange = explode(" - ", $this->soldRangeTime);
            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($soldRange[0]) {
                $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($soldRange[0]))]);
            }
            if ($soldRange[1]) {
                $subQuery->andFilterWhere(['<=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($soldRange[1]))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if ($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if ($this->client_phone) {
            $this->client_phone = (strpos($this->client_phone, '+') === 0 ? '+' : '') . str_replace('+', '', $this->client_phone);
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if ($this->quote_pnr) {
            //$subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            //$query->andWhere(['IN', 'leads.id', $subQuery]);

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"' . $this->quote_pnr . '"%\'')]);
        }

        if ($this->supervision_id > 0) {
            if (
                $this->id
                || $this->uid
                || $this->client_id
                || $this->client_email
                || $this->client_phone
                || $this->status == Lead::STATUS_FOLLOW_UP
                || $this->request_ip
                || $this->discount_id
                || $this->gid
                || $this->bo_flight_id
            ) {
            } else {
                if ($this->statuses && in_array(Lead::STATUS_FOLLOW_UP, $this->statuses) && count($this->statuses) == 1) {
                } elseif ($this->statuses && in_array(Lead::STATUS_PENDING, $this->statuses) && count($this->statuses) == 1) {
                } else {
                    $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
                    $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
                    $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
                }
            }
        }

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'notes_for_experts', $this->notes_for_experts])
            //->andFilterWhere(['like', 'request_ip', $this->request_ip])
            ->andFilterWhere(['like', 'request_ip_detail', $this->request_ip_detail])
            ->andFilterWhere(['like', 'offset_gmt', $this->offset_gmt])
            ->andFilterWhere(['like', 'discount_id', $this->discount_id]);

        if (!empty($this->origin_airport)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if (!empty($this->destination_airport)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if (!empty($this->origin_country)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.a_country_code',$this->origin_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.a_country_code',$this->origin_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }
        if (!empty($this->destination_country)) {
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.a_country_code',$this->destination_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports', 'airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.a_country_code',$this->destination_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        $query->addSelect([
            'origin' => (new Query())
                ->select(['SUBSTRING_INDEX(group_concat(' . LeadFlightSegment::tableName() . '.origin SEPARATOR "-"),' . '"-"' . ',1)'])
                ->from(LeadFlightSegment::tableName())
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id'),
            'destination' => (new Query())
                ->select(['SUBSTRING_INDEX(group_concat(' . LeadFlightSegment::tableName() . '.destination SEPARATOR "-"),' . '"-"' . ',1)'])
                ->from(LeadFlightSegment::tableName())
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id')

        ]);

        $query->addSelect([
            'originCityFullName' => (new Query())
                ->select(['SUBSTRING_INDEX(group_concat(city SEPARATOR "--"),' . '"--"' . ',1)'])
                ->from(LeadFlightSegment::tableName())->leftJoin(Airports::tableName(), LeadFlightSegment::tableName() . '.origin =' . Airports::tableName() . '.iata')
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id'),
            'destinationCityFullName' => (new Query())
                ->select(['SUBSTRING_INDEX(group_concat(city SEPARATOR "--"),' . '"--"' . ',1)'])
                ->from(LeadFlightSegment::tableName())->leftJoin(Airports::tableName(), LeadFlightSegment::tableName() . '.destination =' . Airports::tableName() . '.iata')
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id'),
        ]);

        $query->addSelect([
            'originCountry' => (new Query())
                ->select(['SUBSTRING_INDEX(group_concat(a_country_code SEPARATOR "-"),' . '"-"' . ',1)'])
                ->from(LeadFlightSegment::tableName())->leftJoin(Airports::tableName(), LeadFlightSegment::tableName() . '.origin =' . Airports::tableName() . '.iata')
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id'),
            'destinationCountry' => (new Query())
                ->select(['SUBSTRING_INDEX(group_concat(a_country_code SEPARATOR "-"),' . '"-"' . ',1)'])
                ->from(LeadFlightSegment::tableName())->leftJoin(Airports::tableName(), LeadFlightSegment::tableName() . '.destination =' . Airports::tableName() . '.iata')
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'profit' => (new Query())
                ->select([
                    (new Query())->select(['SUM(' . QuotePrice::tableName() . '.selling' . '-' . QuotePrice::tableName() . '.net' . '+' .   'CASE WHEN ' . Quote::tableName() . '.check_payment' . ' THEN CASE WHEN ' . Quote::tableName() . '.service_fee_percent' . '  THEN ' . Quote::tableName() . '.service_fee_percent' . ' ELSE (' . QuotePrice::tableName() . '.selling' . '*' . (new Quote())->serviceFee . ' * 100) / 100 END ELSE 0 END'  . ')'])
                        ->from(QuotePrice::tableName())
                        ->where(QuotePrice::tableName() . '.quote_id = ' . Quote::tableName() . '.id') ])
                ->from(Quote::tableName())
                ->where(Quote::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['=', Quote::tableName() . '.status', [Quote::STATUS_APPLIED, Quote::STATUS_SENT]])
                ->andWhere(Lead::tableName() . '.status=' . Lead::STATUS_SOLD)
        ]);

        $query->addSelect([
            'quotes' => (new Query())
                ->select(['count(*)'])
                ->from(Quote::tableName())
                ->where(Quote::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'expertQuotes' => (new Query())
                ->select(['count(*)'])
                ->from(Quote::tableName())
                ->where(Quote::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(Quote::tableName() . '.created_by_seller = 0')
        ]);

        $query->addSelect([
            'outboundDate' => (new Query())
                ->select([LeadFlightSegment::tableName() . '.departure'])
                ->from(LeadFlightSegment::tableName())
                ->where(LeadFlightSegment::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->orderBy(['departure' => SORT_ASC])
                ->limit(1)
        ]);

        $query->addSelect([
            'projectInfo' => (new Query())
            ->select([Project::tableName() . '.name'])
            ->from(Project::tableName())
            ->where(Project::tableName() . '.id =' . Lead::tableName() . '.project_id')
        ]);

        $query->addSelect([
            'marketInfo' => (new Query())
                ->select([Sources::tableName() . '.name'])
                ->from(Sources::tableName())
                ->where(Sources::tableName() . '.id =' . Lead::tableName() . '.source_id')
        ]);

        $query->addSelect([
            'segments' => (new Query())
                ->select(['GROUP_CONCAT(CONCAT(' . LeadFlightSegment::tableName() . '.origin' . ',' . '\'->\',' . LeadFlightSegment::tableName() . '.destination))'])
                ->from(LeadFlightSegment::tableName())
                ->where(LeadFlightSegment::tableName() . '.lead_id=' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'customerEmail' => (new Query())
                ->select([
                    (new Query())->select(['GROUP_CONCAT(' . ClientEmail::tableName() . '.email)'])
                        ->from(ClientEmail::tableName())
                        ->where(ClientEmail::tableName() . '.client_id = ' . Client::tableName() . '.id') ])
                ->from(Client::tableName())
                ->where(Client::tableName() . '.id = ' . Lead::tableName() . '.client_id')
        ]);

        $query->addSelect([
            'statusDate' => (new Query())
                ->select([LeadFlow::tableName() . '.created'])
                ->from(LeadFlow::tableName())
                ->andWhere(LeadFlow::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(LeadFlow::tableName() . '.status = ' . Lead::tableName() . '.status')
                ->orderBy(['created' => SORT_DESC])
                ->limit(1)
        ]);

        $query->addSelect([
            'grade' => (new Query())
                ->select(['count(*)'])
                ->from(LeadFlow::tableName())
                ->andWhere(LeadFlow::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(LeadFlow::tableName() . '.status = ' . Lead::STATUS_FOLLOW_UP)
        ]);

        $query->addSelect([
            'inCalls' => (new Query())
                ->select(['count(*)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_IN])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'inCallsDuration' => (new Query())
                ->select(['sum(' . Call::tableName() . '.c_recording_duration)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_IN])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'outCalls' => (new Query())
                ->select(['count(*)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_OUT])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'outCallsDuration' => (new Query())
                ->select(['sum(' . Call::tableName() . '.c_recording_duration)'])
                ->from(Call::tableName())
                ->andWhere([Call::tableName() . '.c_call_type_id' => Call::CALL_TYPE_OUT])
                ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                ->andWhere(['>=', Call::tableName() . '.c_recording_duration', 15])
        ]);

        $query->addSelect([
            'smsOffers' => (new Query())
                ->select(['count(*)'])
                ->from(Sms::tableName())
                ->andWhere([Sms::tableName() . '.s_template_type_id' => 2])
                ->andWhere(Sms::tableName() . '.s_lead_id = ' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'emailOffers' => (new Query())
                ->select(['count(*)'])
                ->from(Email::tableName())
                ->andWhere([Email::tableName() . '.e_template_type_id' => 1])
                ->andWhere(Email::tableName() . '.e_lead_id = ' . Lead::tableName() . '.id')
        ]);

        $query->addSelect([
            'quoteType' => (new Query())
                ->select([Quote::tableName() . '.created_by_seller'])
                ->from(Quote::tableName())
                ->andWhere([Quote::tableName() . '.status' => Quote::STATUS_APPLIED])
                ->andWhere(Quote::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                ->limit(1)
        ]);

        $query->addSelect([
            'agentName' => (new Query())
            ->select([Employee::tableName() . '.username'])
            ->from(Employee::tableName())
            ->where(Employee::tableName() . '.id = ' . Lead::tableName() . '.employee_id')
        ]);

        $command = $query->createCommand();

        return $command->queryAll();
    }

    public function searchAgent($params, Employee $user)
    {
        $query = Lead::find();
        $query->with(['leadFlows']);
        $query->with(['project', 'lDep', 'source', 'employee', 'client', 'client.clientEmails', 'client.clientPhones', 'leadFlightSegments']);
        $query->select([Lead::tableName() . '.*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        $query->andWhere([
            Lead::tableName() . '.project_id' => ProjectEmployeeAccess::find()
                ->select(ProjectEmployeeAccess::tableName() . '.project_id')
                ->andWhere([ProjectEmployeeAccess::tableName() . '.employee_id' => $user->id])
        ]);
        $query->andWhere([
            Lead::tableName() . '.l_dep_id' => UserDepartment::find()
                ->select(UserDepartment::tableName() . '.ud_dep_id')
                ->andWhere([UserDepartment::tableName() . '.ud_user_id' => $user->id])
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['leads.id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            'leads.id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ]
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $additionalRestriction = ($this->id || $this->client_email || $this->client_phone || $this->hybrid_uid || $this->quote_pnr);

        if (!$additionalRestriction) {
            $query->andWhere(['<>', 'status', Lead::STATUS_PENDING]);
//            $query->andWhere(['IN', Lead::tableName() . '.project_id', $projectIds]);
            $this->employee_id = Yii::$app->user->id;
        }

        $query->andFilterWhere([
            Lead::tableName() . '.id' => $this->id,
            'gid' => $this->gid,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,

            'uid' => $this->uid,
            'trip_type' => $this->trip_type,
            'cabin' => $this->cabin,
            'request_ip' => $this->request_ip,
            'discount_id' => $this->discount_id,
            'l_answered'    => $this->l_answered,
            'hybrid_uid' => $this->hybrid_uid,
        ]);

        if ($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }

        if ($this->created_date_from || $this->created_date_to) {
            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }
        } else {
            if ($this->created) {
                $query->andFilterWhere(['DATE(created)' => date('Y-m-d', strtotime($this->created))]);
            }
        }

        if ($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '" . date('Y-m-d', strtotime($departRange[0])) . "'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '" . date('Y-m-d', strtotime($departRange[1])) . "'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if ($this->client_name) {
                    $q->where(['=', 'clients.last_name', $this->client_name])
                        ->orWhere(['=', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }
        if ($this->client_phone) {
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['phone' => $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }
        if ($this->quote_pnr) {
            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"' . $this->quote_pnr . '"%\'')]);
        }
        if ($this->quoteTypeId) {
            $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['type_id' => $this->quoteTypeId])->groupBy('lead_id');
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }
        if ($this->expiration_dt) {
            $query->andWhere(new Expression(
                'DATE(l_expiration_dt) = :date',
                [':date' => date('Y-m-d', strtotime($this->expiration_dt))]
            ));
        }

        if ($this->excludeBonusQueue) {
            $query->andFilterWhere(['<>', 'leads.status', Lead::STATUS_FOLLOW_UP]);
        }

        if ($this->excludeExtraQueue) {
            $query->andFilterWhere(['<>', 'leads.status', Lead::STATUS_EXTRA_QUEUE]);
        }

        if ($this->lead_data_key) {
             $query->andWhere([
                'IN',
                'leads.id',
                LeadData::find()
                    ->select(['DISTINCT(ld_lead_id)'])
                    ->where(['ld_field_key' => $this->lead_data_key])
                    ->groupBy('ld_lead_id')
             ]);
        }

        if ($this->lead_data_value !== '') {
             $query->andWhere([
                'IN',
                'leads.id',
                LeadData::find()
                    ->select(['DISTINCT(ld_lead_id)'])
                    ->where(['ld_field_value' => $this->lead_data_value])
                    ->groupBy('ld_lead_id')
             ]);
        }

        return $dataProvider;
    }

    public function searchByCase($params)
    {
        $projectIds = array_keys(EmployeeProjectAccess::getProjects());
        $query = Lead::find();
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [Lead::tableName() . '.id' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            Lead::tableName() . '.id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() . '.id' => SORT_DESC]
            ]
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['IN', Lead::tableName() . '.project_id', $projectIds]);

        if (
            $this->id
            || $this->uid
            || $this->gid
            || $this->quote_pnr
            || $this->client_id
            || $this->client_name
            || $this->client_email
            || $this->client_phone
            || $this->bo_flight_id
            || $this->employee_id
            || $this->request_ip
        ) {
        } else {
            $query->where('0=1');
            $this->employee_id = Yii::$app->user->id;
        }

        //VarDumper::dump($params, 10, true); exit;

        // grid filtering conditions
        $query->andFilterWhere([
            Lead::tableName() . '.id' => $this->id,
//            'gid' => $this->gid,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            Lead::tableName() . '.status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'bo_flight_id' => $this->bo_flight_id,

            'uid' => $this->uid,
            'trip_type' => $this->trip_type,
            'cabin' => $this->cabin,
            'request_ip' => $this->request_ip,
        ]);


        if ($this->statuses) {
            $query->andWhere([Lead::tableName() . '.status' => $this->statuses]);
        }
        $query->andWhere(['<>', Lead::tableName() . '.status', Lead::STATUS_PENDING]);

        if ($this->created_date_from || $this->created_date_to) {
            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }
        } else {
            if ($this->created) {
                $query->andFilterWhere(['DATE(created)' => date('Y-m-d', strtotime($this->created))]);
            }
        }

        if ($this->depart_date_from || $this->depart_date_to) {
            $having = [];
            if ($this->depart_date_from) {
                $having[] = "MIN(departure) >= '" . date('Y-m-d', strtotime($this->depart_date_from)) . "'";
            }
            if ($this->depart_date_to) {
                $having[] = "MIN(departure) <= '" . date('Y-m-d', strtotime($this->depart_date_to)) . "'";
            }

            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

//        if($this->client_name) {
//            $query->joinWith(['client' => function ($q) {
//                if($this->client_name) {
//                    $q->where(['=', 'clients.last_name', $this->client_name])
//                        ->orWhere(['=', 'clients.first_name', $this->client_name]);
//                }
//            }]);
//        }

        if ($this->client_name) {
            $subQuery = Client::find()->select(['clients.id'])->distinct('clients.id')->where(['=', 'clients.last_name', $this->client_name])
                ->orWhere(['=', 'clients.first_name', $this->client_name]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if ($this->client_phone) {
            $this->client_phone = (strpos($this->client_phone, '+') === 0 ? '+' : '') . str_replace('+', '', $this->client_phone);
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['=', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if ($this->quote_pnr) {
            /* $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]); */

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"' . $this->quote_pnr . '"%\'')]);
        }

        /*$sqlRaw = $query->createCommand()->getRawSql();
          VarDumper::dump($sqlRaw, 10, true); exit;*/

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchSold($params, Employee $user): ActiveDataProvider
    {
        $query = $this->leadBadgesRepository->getSoldQuery($user)/*->with('project', 'source', 'employee')->joinWith('leadFlowSold')*/;
        //$query->with(['client', 'client.clientEmails', 'client.clientPhones', 'leadFlightSegments']);
        $this->load($params);
        $leadTable = Lead::tableName();

        //$query->select([$leadTable . '.*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'bo_flight_id',
                    'project_id',
                    'source_id',
                    'last_ticket_date' => [
                        'asc' => [LeadFlow::tableName() . '.created' => SORT_ASC],
                        'desc' => [LeadFlow::tableName() . '.created' => SORT_DESC],
                    ],
                    'l_status_dt'
                ],
                'defaultOrder' => ['l_status_dt' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.bo_flight_id' => $this->bo_flight_id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.employee_id' => $this->employee_id,
            $leadTable . '.l_type' => $this->l_type,
            'DATE(l_status_dt)' => $this->l_status_dt,
        ]);

        if ($this->updated) {
            $query->andFilterWhere(['=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->updated))]);
        }

        if ($this->sold_date_from) {
            $query->andFilterWhere(['>=', 'l_status_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->sold_date_from))]);
        }
        if ($this->sold_date_to) {
            $query->andFilterWhere(['<', 'l_status_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->sold_date_to))]);
        }

        /*if ($this->last_ticket_date) {
            $subQuery = LeadFlow::find()->select(['lead_flow.lead_id'])->distinct('lead_flow.lead_id')->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');
            $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_ticket_date))])
                ->andFilterWhere(['<=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_ticket_date) + 3600 * 24)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->sold_date_from || $this->sold_date_to) {
            $subQuery = LeadFlow::find()->select(['lead_flow.lead_id'])->distinct('lead_flow.lead_id')->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($this->sold_date_from) {
                $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->sold_date_from))]);
            }
            if ($this->sold_date_to) {
                $subQuery->andFilterWhere(['<', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->sold_date_to))]);
            }
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }*/

//        if($this->employee_id){
//            $query
//            ->leftJoin(ProfitSplit::tableName().' ps','ps.ps_lead_id = leads.id')
//            ->leftJoin(TipsSplit::tableName().' ts','ts.ts_lead_id = leads.id')
//            ->andWhere($leadTable.'.employee_id = '. $this->employee_id.' OR ps.ps_user_id ='.$this->employee_id.' OR ts.ts_user_id ='.$this->employee_id)
//            ->groupBy(['leads.id']);
//        }

//         $sqlRaw = $query->createCommand()->getRawSql();
//        VarDumper::dump($sqlRaw, 10, true); exit;

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchSoldKpi($params)
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
        $query = Lead::find();
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.client_id' => $this->client_id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.bo_flight_id' => $this->bo_flight_id,
            $leadTable . '.rating' => $this->rating,
        ]);

        $query
            ->andWhere(['leads.status' => Lead::STATUS_SOLD]);
        //->andWhere(['IN', $leadTable . '.project_id', $projectIds])

        if (!empty($this->updated)) {
            $query->andFilterWhere(['=','DATE(leads.updated)', date('Y-m-d', strtotime($this->updated))]);
        }

        if ($this->sold_date_from || $this->sold_date_to) {
            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($this->sold_date_from) {
                $subQuery->andFilterWhere(['>=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_from))]);
            }
            if ($this->sold_date_to) {
                $subQuery->andFilterWhere(['<=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_to))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        //echo $this->created_date_from;
        if ($this->quote_pnr) {
            $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        if ($this->employee_id) {
            $query
                ->leftJoin(ProfitSplit::tableName() . ' ps', 'ps.ps_lead_id = leads.id')
                ->leftJoin(TipsSplit::tableName() . ' ts', 'ts.ts_lead_id = leads.id')
                ->andWhere($leadTable . '.employee_id = ' . $this->employee_id . ' OR ps.ps_user_id =' . $this->employee_id . ' OR ts.ts_user_id =' . $this->employee_id)
                ->groupBy(['leads.id']);
        } else {
            $query->andWhere('1=2');
        }

        $query->with(['employee']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchBooked($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find()->with('project');
        $query = $this->leadBadgesRepository->getBookedQuery($user)->with('project');

        $this->load($params);

        $leadTable = Lead::tableName();

        $query->select([$leadTable . '.*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.uid' => $this->uid,
            $leadTable . '.bo_flight_id' => $this->bo_flight_id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.employee_id' => $this->employee_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->created) {
            $query->andFilterWhere(['>=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'leads.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        $query->with(['employee']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchClosed($params, Employee $user): ActiveDataProvider
    {
        $query = $this->leadBadgesRepository->getClosedQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.client_id' => $this->client_id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_init_price' => $this->l_init_price,
            $leadTable . '.l_is_test' => $this->l_is_test,
            $leadTable . '.l_call_status_id' => $this->l_call_status_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->limit > 0) {
            $query->limit($this->limit);
            //$dataProvider->setTotalCount($this->limit);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchProcessing($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find()->with('project');
        $query = $this->leadBadgesRepository->getProcessingQuery($user)->with('project');
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $query->with(['leadFlows']);

        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['l_last_action_dt' => SORT_DESC],
                'attributes' => [
                    'id',
                    'updated',
                    'created',
                    'status',
                    'l_last_action_dt',
                    'expiration_dt',
                    'extra_timer',
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            'expiration_dt' => [
                'asc' => [Lead::tableName() . '.l_expiration_dt' => SORT_ASC],
                'desc' => [Lead::tableName() . '.l_expiration_dt' => SORT_DESC]
            ],
            'extra_timer' => [
                'asc' => [LeadPoorProcessing::tableName() . '.lpp_expiration_dt' => SORT_ASC],
                'desc' => [LeadPoorProcessing::tableName() . '.lpp_expiration_dt' => SORT_DESC]
            ],
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        if ($this->lead_user_rating) {
            $leadIds = LeadUserRatingQuery::getLeadIdsByUserAndRating($user->id, $this->lead_user_rating);
                $query->andWhere([
                    'in',
                    'id',
                    $leadIds
                ]);
        }
        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->l_last_action_dt) {
            $query->andFilterWhere(['>=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt))])
                ->andFilterWhere(['<=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.employee_id' => $this->employee_id,
            $leadTable . '.status' => $this->status,
            $leadTable . '.l_answered' => $this->l_answered,
            $leadTable . '.l_init_price' => $this->l_init_price,
            $leadTable . '.l_type' => $this->l_type,
        ]);

//        $query->andWhere(['IN','leads.status', [self::STATUS_SNOOZE, self::STATUS_PROCESSING, self::STATUS_ON_HOLD]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds]);

        if ($this->email_status > 0) {
            if ($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if ($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SENT, Quote::STATUS_OPENED] ]);
            if ($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') > 0'));
            } else {
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') = 0'));
            }
        }

//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'leadChecklists', 'leadChecklists.lcType', 'employee']);
        $query->with([
           'minLpp' => static function (ActiveQuery $query) {
                $query->joinWith('lppLppd')->andWhere(['lppd_enabled' => 1]);
           }
        ]);
        $lppTableName = LeadPoorProcessing::tableName();
        $onCondition = new Expression($lppTableName . '.lpp_lead_id = leads.id AND ' . $lppTableName . '.lpp_expiration_dt = (SELECT MIN(lpp_expiration_dt) FROM ' . $lppTableName . ' WHERE lpp_lead_id = leads.id)');
        $query->leftJoin(LeadPoorProcessing::tableName(), $onCondition);

        if ($this->expiration_dt) {
            $query->andWhere(new Expression(
                'DATE(l_expiration_dt) = :date',
                [':date' => date('Y-m-d', strtotime($this->expiration_dt))]
            ));
        }

//        print_r($query->createCommand()->rawSql);die;

        return $dataProvider;
    }

    public function searchAlternative($params, Employee $user, ?int $limit): ActiveDataProvider
    {
        $this->limit = $limit;
        $query = $this->leadBadgesRepository->getAlternativeQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            'expiration_dt' => [
                'asc' => [Lead::tableName() . '.l_expiration_dt' => SORT_ASC],
                'desc' => [Lead::tableName() . '.l_expiration_dt' => SORT_DESC]
            ],
        ]);
        $dataProvider->setSort($sort);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if (empty($params['is_test']) && !$user->checkIfUsersIpIsAllowed()) {
            $query->andWhere([Lead::tableName() . '.l_is_test' => 0]);
        }

        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        if ($user->isAdmin()) {
            $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'project', 'leadFlightSegments']);
        }

        if ($this->expiration_dt) {
            $query->andWhere(new Expression(
                'DATE(l_expiration_dt) = :date',
                [':date' => date('Y-m-d', strtotime($this->expiration_dt))]
            ));
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchFollowUp($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
        $query = $this->leadBadgesRepository->getFollowUpQuery($user)->with('project');
        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        $leadTable = Lead::tableName();

        $departureQuery =  (new Query())
            ->select(['lfs.departure'])
            ->from(['lfs' => LeadFlightSegment::tableName()])
            ->where('lfs.lead_id  = ' . $leadTable . '.id')
            ->orderBy(['lfs.departure' => SORT_ASC])
            ->limit(1)
            ->createCommand()->getSql();
        $nowQuery = (new Query())
            ->select(new Expression("if (a.dst is not null, if (cast(a.dst as signed) >= 0, concat('+', if (length(a.dst) < 2, concat(0, a.dst), a.dst),':00'), concat(a.dst, ':00')), '+00:00')"))
            ->from(['a' => Airports::tableName()])
            ->andWhere('a.iata = (' .
                (new Query())
                    ->select(['lfs.origin'])
                    ->from(['lfs' => LeadFlightSegment::tableName()])
                    ->andWhere('lfs.lead_id = ' . $leadTable . '.id')
                    ->orderBy(['lfs.departure' => SORT_ASC])
                    ->limit(1)
                    ->createCommand()->getSql()
                . ')')
            ->createCommand()->getSql();

        $query->addSelect([
            'remainingDays' =>
                new Expression("datediff((" . $departureQuery . "), (date(convert_tz(NOW(), '+00:00', (" . $nowQuery . ")))))")
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['l_last_action_dt' => SORT_DESC],
                'attributes' => [
                    'id',
                    'created',
                    'l_last_action_dt',
                    'remainingDays'
                ]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created) {
            //$query->andFilterWhere(['=','DATE(created)', date('Y-m-d', strtotime($this->created))]);
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->l_last_action_dt) {
            //$query->andFilterWhere(['=','DATE(l_last_action_dt)', date('Y-m-d', strtotime($this->l_last_action_dt))]);
            $query->andFilterWhere(['>=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt))])
                ->andFilterWhere(['<=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.l_answered' => $this->l_answered,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

//        $query
//        ->andWhere(['IN','leads.status', [self::STATUS_FOLLOW_UP]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
//        ;

        $showAll = Yii::$app->request->cookies->getValue(Lead::getCookiesKey(), true);
        if (!$showAll) {
            $query->andWhere([
                'NOT IN', Lead::tableName() . '.id', Lead::unprocessedByAgentInFollowUp()
            ]);
        }

        if ($this->email_status > 0) {
            if ($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if ($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SENT, Quote::STATUS_OPENED] ]);
            if ($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') > 0'));
            } else {
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') = 0'));
            }
        }

//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }

        if ($this->remainingDays || $this->remainingDays === 0) {
            $query->andHaving(['remainingDays' => $this->remainingDays]);
        }

        $query->with([
            'client',
            'client.clientEmails',
            'client.clientPhones',
            'employee',
            'leadFlightSegments' => static function (ActiveQuery $query) {
                return $query->orderBy(['id' => SORT_ASC]);
            },
//            'leadFlightSegments.airportByOrigin'
        ]);

//          $sqlRaw = $query->createCommand()->getRawSql();
//         VarDumper::dump($sqlRaw, 10, true); exit;

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchBonus($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
        $query = $this->leadBadgesRepository->getBonusQuery($user)->with('project');
        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        $leadTable = Lead::tableName();

        $departureQuery =  (new Query())
            ->select(['lfs.departure'])
            ->from(['lfs' => LeadFlightSegment::tableName()])
            ->where('lfs.lead_id  = ' . $leadTable . '.id')
            ->orderBy(['lfs.departure' => SORT_ASC])
            ->limit(1)
            ->createCommand()->getSql();
        $nowQuery = (new Query())
            ->select(new Expression("if (a.dst is not null, if (cast(a.dst as signed) >= 0, concat('+', if (length(a.dst) < 2, concat(0, a.dst), a.dst),':00'), concat(a.dst, ':00')), '+00:00')"))
            ->from(['a' => Airports::tableName()])
            ->andWhere('a.iata = (' .
                (new Query())
                    ->select(['lfs.origin'])
                    ->from(['lfs' => LeadFlightSegment::tableName()])
                    ->andWhere('lfs.lead_id = ' . $leadTable . '.id')
                    ->orderBy(['lfs.departure' => SORT_ASC])
                    ->limit(1)
                    ->createCommand()->getSql()
                . ')')
            ->createCommand()->getSql();

        $query->addSelect([
            'remainingDays' =>
                new Expression("datediff((" . $departureQuery . "), (date(convert_tz(NOW(), '+00:00', (" . $nowQuery . ")))))")
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['l_last_action_dt' => SORT_DESC],
                'attributes' => [
                    'id',
                    'created',
                    'l_last_action_dt',
                    'remainingDays'
                ]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created) {
            //$query->andFilterWhere(['=','DATE(created)', date('Y-m-d', strtotime($this->created))]);
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->l_last_action_dt) {
            //$query->andFilterWhere(['=','DATE(l_last_action_dt)', date('Y-m-d', strtotime($this->l_last_action_dt))]);
            $query->andFilterWhere(['>=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt))])
                ->andFilterWhere(['<=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.l_answered' => $this->l_answered,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

//        $query
//        ->andWhere(['IN','leads.status', [self::STATUS_FOLLOW_UP]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
//        ;

//        $showAll = Yii::$app->request->cookies->getValue(Lead::getCookiesKey(), true);
//        if (!$showAll) {
//            $query->andWhere([
//                'NOT IN', Lead::tableName() . '.id', Lead::unprocessedByAgentInFollowUp()
//            ]);
//        }

        if ($this->email_status > 0) {
            if ($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if ($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SENT, Quote::STATUS_OPENED] ]);
            if ($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') > 0'));
            } else {
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') = 0'));
            }
        }

//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }

        if ($this->remainingDays || $this->remainingDays === 0) {
            $query->andHaving(['remainingDays' => $this->remainingDays]);
        }

        $query->with([
            'client',
            'client.clientEmails',
            'client.clientPhones',
            'employee',
            'leadFlightSegments' => static function (ActiveQuery $query) {
                return $query->orderBy(['id' => SORT_ASC]);
            },
//            'leadFlightSegments.airportByOrigin'
        ]);

//          $sqlRaw = $query->createCommand()->getRawSql();
//         VarDumper::dump($sqlRaw, 10, true); exit;

        return $dataProvider;
    }

//    /**
//     * @param $params
//     * @return ActiveDataProvider
//     */
//    public function searchFollowUp($params)
//    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find()->with('project');
//        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
//
//        $leadTable = Lead::tableName();
//
//        // add conditions that should always apply here
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'sort'=> ['defaultOrder' => ['l_last_action_dt' => SORT_DESC],'attributes' => ['id','updated','created','status','l_last_action_dt']],
//            'pagination' => [
//                'pageSize' => 30,
//            ],
//        ]);
//
//        $this->load($params);
//
//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
//
//        if (isset($params['LeadSearch']['created'])) {
//            $query->andFilterWhere(['=','DATE(created)', $this->created]);
//        }
//
//        if (isset($params['LeadSearch']['l_last_action_dt'])) {
//            $query->andFilterWhere(['=','DATE(l_last_action_dt)', $this->l_last_action_dt]);
//        }
//
//        // grid filtering conditions
//        $query->andFilterWhere([
//            $leadTable.'.id' => $this->id,
//            $leadTable.'.client_id' => $this->client_id,
//            $leadTable.'.employee_id' => $this->employee_id,
//            $leadTable.'.project_id' => $this->project_id,
//            $leadTable.'.source_id' => $this->source_id,
//            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
//            $leadTable.'.rating' => $this->rating,
//            $leadTable.'.status' => $this->status,
//            $leadTable.'.l_answered' => $this->l_answered,
//        ]);
//
//
//        $query
//        ->andWhere(['IN','leads.status', [self::STATUS_FOLLOW_UP]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
//        ;
//
//        $showAll = Yii::$app->request->cookies->getValue(Lead::getCookiesKey(), true);
//        if (!$showAll) {
//            $query->andWhere([
//                'NOT IN', Lead::tableName() . '.id', Lead::unprocessedByAgentInFollowUp()
//            ]);
//        }
//
//        if($this->email_status > 0) {
//            if($this->email_status == 2) {
//                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
//            } else {
//                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
//            }
//        }
//
//        if($this->quote_status > 0) {
//            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SENT, Quote::STATUS_OPENED] ]);
//            if($this->quote_status == 2) {
//                //echo $subQuery->createCommand()->getRawSql(); exit;
//                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') > 0'));
//            } else {
//                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') = 0'));
//            }
//        }
//
//
//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }
//
//        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee']);
//
//
//        /*  $sqlRaw = $query->createCommand()->getRawSql();
//         VarDumper::dump($sqlRaw, 10, true); exit; */
//
//        return $dataProvider;
//    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchPending($params, Employee $user): ActiveDataProvider
    {
        $query = $this->leadBadgesRepository->getPendingQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.client_id' => $this->client_id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_init_price' => $this->l_init_price,
            $leadTable . '.l_is_test' => $this->l_is_test,
            $leadTable . '.l_call_status_id' => $this->l_call_status_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->limit > 0) {
            $query->limit($this->limit);
            //$dataProvider->setTotalCount($this->limit);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchBusinessInbox($params, Employee $user): ActiveDataProvider
    {
        $query = $this->leadBadgesRepository->getBusinessInboxQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.client_id' => $this->client_id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_init_price' => $this->l_init_price,
            $leadTable . '.l_is_test' => $this->l_is_test,
            $leadTable . '.l_call_status_id' => $this->l_call_status_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->limit > 0) {
            $query->limit($this->limit);
            //$dataProvider->setTotalCount($this->limit);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchNew($params, Employee $user): ActiveDataProvider
    {
        $leadTable = Lead::tableName();

        $query = Lead::find()->andWhere([$leadTable . '.status' => Lead::STATUS_NEW]);

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.client_id' => $this->client_id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_init_price' => $this->l_init_price,
            $leadTable . '.l_is_test' => $this->l_is_test,
            $leadTable . '.l_call_status_id' => $this->l_call_status_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchInbox($params, Employee $user): ActiveDataProvider
    {
        $query = $this->leadBadgesRepository->getInboxQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if (empty($params['is_test']) && !$user->checkIfUsersIpIsAllowed()) {
            $query->andWhere([Lead::tableName() . '.l_is_test' => 0]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->limit > 0) {
            $query->limit($this->limit);
            //$dataProvider->setTotalCount($this->limit);
        }

        if ($user->isAdmin()) {
            $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'project', 'leadFlightSegments']);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchFailedBookings($params, Employee $user, ?int $limit): ActiveDataProvider
    {
        $this->limit = $limit;
        $query = $this->leadBadgesRepository->getFailedBookingsQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes = array_merge($sort->attributes, [
            'expiration_dt' => [
                'asc' => [Lead::tableName() . '.l_expiration_dt' => SORT_ASC],
                'desc' => [Lead::tableName() . '.l_expiration_dt' => SORT_DESC]
            ],
        ]);
        $dataProvider->setSort($sort);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if (empty($params['is_test']) && !$user->checkIfUsersIpIsAllowed()) {
            $query->andWhere([Lead::tableName() . '.l_is_test' => 0]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->limit > 0) {
            $query->limit($this->limit);
            //$dataProvider->setTotalCount($this->limit);
        }

        if ($user->isAdmin()) {
            $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'project', 'leadFlightSegments']);
        }

        if ($this->expiration_dt) {
            $query->andWhere(new Expression(
                'DATE(l_expiration_dt) = :date',
                [':date' => date('Y-m-d', strtotime($this->expiration_dt))]
            ));
        }

        return $dataProvider;
    }

//
//    /**
//     * @param $params
//     * @return ActiveDataProvider
//     */
//    public function searchInbox($params)
//    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find();
//        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
//        $leadTable = Lead::tableName();
//
//
//        // add conditions that should always apply here
//        $this->load($params);
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'sort'=> ['defaultOrder' => ['created' => SORT_DESC]],
//            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
//        ]);
//
//
//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
//
//        if (isset($params['LeadSearch']['created'])) {
//            $query->andFilterWhere(['=','DATE(created)', $this->created]);
//        }
//
//        // grid filtering conditions
//        $query->andFilterWhere([
//            $leadTable.'.id' => $this->id,
//            $leadTable.'.client_id' => $this->client_id,
//            $leadTable.'.project_id' => $this->project_id,
//            $leadTable.'.source_id' => $this->source_id,
//            $leadTable.'.status' => $this->status,
//        ]);
//
//        $query->andWhere(['IN','leads.status', [self::STATUS_PENDING]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds]);
//
//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }
//
//        if($this->limit > 0) {
//            $query->limit($this->limit);
//            //$dataProvider->setTotalCount($this->limit);
//        }
//
//        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);
//
//        return $dataProvider;
//    }
//

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function searchTrash($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find()->with('project', 'leadFlightSegments');

        $query = $this->leadBadgesRepository->getTrashQuery($user)->with('project', 'leadFlightSegments');
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        //$query->with('lastLeadFlow');
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC],
                'attributes' => [
                    'id',
                    'project_id',
                    'created',
                    'updated'
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->updated) {
            $query->andFilterWhere(['>=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated))])
                ->andFilterWhere(['<=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated) + 3600 * 24)]);
        }

        if ($this->date_range && $this->datetime_start && $this->datetime_end && empty($this->created) && empty($this->updated)) {
            $query->andFilterWhere(['>=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.employee_id' => $this->employee_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if (ArrayHelper::isIn($this->is_conversion, ['1', '0'], false)) {
            $leadIds = LeadUserConversion::find()
                ->select('luc_lead_id')
                ->groupBy(['luc_lead_id'])
                ->indexBy('luc_lead_id')
                ->column();
            $command = $this->is_conversion ? 'IN' : 'NOT IN';

            $query->andWhere([
                $command,
                $leadTable . '.id',
                $leadIds
            ]);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchDuplicate($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find();
        $query = $this->leadBadgesRepository->getDuplicateQuery($user);
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $query->with('lastLeadFlow');
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.l_duplicate_lead_id' => $this->l_duplicate_lead_id,
            $leadTable . '.l_request_hash' => $this->l_request_hash,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.employee_id' => $this->employee_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

//        $query
//            ->andWhere(['leads.status' => self::STATUS_TRASH])
//            ->andWhere(['IS NOT','leads.l_duplicate_lead_id', NULL])
//            //->andWhere(['IN', $leadTable . '.project_id', $projectIds])
//        ;
//
//
//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee', 'project']);

        return $dataProvider;
    }

    public function searchEmail($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(distinct l.id) AS cnt', 'ce.email AS client_email']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'ce.email', null]);
        $query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        if ($this->client_email) {
            $query->andFilterWhere(['like', 'ce.email', $this->client_email]);
        }

        $query->leftJoin('client_email AS ce', 'ce.client_id = l.client_id');
        $query->groupBy('ce.email');
        $query->having(['>', 'cnt', 1]);
        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            //'sort' =>false, to remove the table header sorting
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'client_email' => [
                        'asc' => ['client_email' => SORT_ASC],
                        'desc' => ['client_email' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Email',
                    ],
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    public function searchPhone($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(distinct l.id) AS cnt', 'cp.phone AS client_phone']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'cp.phone', null]);
        $query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        if ($this->client_phone) {
            $query->andFilterWhere(['like', 'cp.phone', $this->client_phone]);
        }

        $query->leftJoin('client_phone AS cp', 'cp.client_id = l.client_id');
        $query->groupBy('cp.phone');
        $query->having(['>', 'cnt', 1]);

        $command = $query->createCommand();
        $sql = $command->rawSql;
        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            //'sort' =>false, to remove the table header sorting
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'client_phone' => [
                        'asc' => ['client_phone' => SORT_ASC],
                        'desc' => ['client_phone' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Phone',
                    ],
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function searchIp($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(distinct l.id) AS cnt', 'l.request_ip']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'l.request_ip', null]);
        $query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        if ($this->request_ip) {
            //$query->andFilterWhere(['like', 'l.request_ip', $this->request_ip]);
            $query->andFilterWhere(['request_ip'    => $this->request_ip]);
        }

        $query->groupBy('l.request_ip');
        $query->having(['>', 'cnt', 1]);

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                    'request_ip' => [
                        'asc' => ['request_ip' => SORT_ASC],
                        'desc' => ['request_ip' => SORT_DESC],
                        'label' => 'IP',
                    ],

                    //'request_ip'
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    public function searchAgentLeads($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select(['e.id', 'e.username']);

        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_SOLD . ') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_ON_HOLD . ') AS st_on_hold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_PROCESSING . ') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_FOLLOW_UP . ') AS st_follow_up ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_TRASH . ') AS st_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_REJECT . ') AS st_reject ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_BOOKED . ') AS st_booked ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_SNOOZE . ') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_PENDING . ') AS st_pending ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id) AS all_statuses']);

        //$query->from('leads AS l');
        $query->from('employees AS e');

        //$query->where(['IS NOT', 'l.employee_id', null]);
        //$query->where(['>', 'all_statuses', 0]);

        // $query->leftJoin(['employee as e', 'l.employee_id=e.id']);

        //$query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        /*if($this->request_ip) {
            $query->andFilterWhere(['like', 'l.request_ip', $this->request_ip]);
        }*/

        //$query->groupBy(['l.status', 'l.employee_id']);
        $query->having(['>', 'all_statuses', 0]);

        $totalCount = 20;

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['st_sold' => SORT_DESC],
                'attributes' => [
                    'st_sold' => [
                        'asc' => ['st_sold' => SORT_ASC],
                        'desc' => ['st_sold' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Sold',
                    ],
                    'st_on_hold' => [
                        'asc' => ['st_sst_on_holdold' => SORT_ASC],
                        'desc' => ['st_on_hold' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Hold',
                    ],
                    'st_processing' => [
                        'asc' => ['st_processing' => SORT_ASC],
                        'desc' => ['st_processing' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Processing',
                    ],
                    'st_follow_up' => [
                        'asc' => ['st_follow_up' => SORT_ASC],
                        'desc' => ['st_follow_up' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Follow up',
                    ],
                    'st_trash' => [
                        'asc' => ['st_trash' => SORT_ASC],
                        'desc' => ['st_trash' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Trash',
                    ],
                    'st_reject' => [
                        'asc' => ['st_reject' => SORT_ASC],
                        'desc' => ['st_reject' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Reject',
                    ],
                    'st_booked' => [
                        'asc' => ['st_booked' => SORT_ASC],
                        'desc' => ['st_booked' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Booked',
                    ],
                    'st_snooze' => [
                        'asc' => ['st_snooze' => SORT_ASC],
                        'desc' => ['st_snooze' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Snooze',
                    ],
                    'st_pending' => [
                        'asc' => ['st_pending' => SORT_ASC],
                        'desc' => ['st_pending' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Pending',
                    ],

                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                        'label' => 'Agent',
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        'label' => 'Agent',
                    ],

                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    /**
     * @param string $category
     * @param string $period
     * @return SqlDataProvider
     */
    public function searchTopAgents(string $category, string $period): SqlDataProvider
    {
        switch ($period) {
            case 'currentWeek':
                $interval = ChartTools::getWeek('0 week');
                break;
            case 'lastWeek':
                $interval = ChartTools::getWeek('-1 week');
                break;
            case 'currentMonth':
                $interval = ChartTools::getCurrentMonth();
                break;
        }

        /**
         * @var $interval array
         */
        $start = date("Y-m-d H:i", $interval['start']);
        $end = date("Y-m-d H:i", $interval['end']);
        $between_condition = " BETWEEN '{$start}' AND '{$end}'";

        $query = new Query();

        if ($category == 'finalProfit') {
            $query->select(['e.id', 'e.username']);
            $query->addSelect(['(SELECT 
            SUM(CASE
                    WHEN
                        employee_id = e.id AND lead_origin = 1
                    THEN
                    final_profit - agents_processing_fee
                    WHEN
                        employee_id = e.id AND lead_origin = 0
                    THEN                  
                    -((final_profit - agents_processing_fee) * (lead_ps / 100)) 
                    WHEN 
                    user_id = e.id AND lead_origin = 0
                    THEN
                    (final_profit - agents_processing_fee) * (lead_ps / 100)
                    ELSE 0
                END)
        FROM
            leads
                LEFT JOIN
            (SELECT 
                employee_id AS user_id,
                    id AS lead_id,
                    100 AS lead_ps,
                    TRUE AS lead_origin
            FROM
                leads
            WHERE               
                leads.id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')) AS unionleads ON id = lead_id
        WHERE
            (updated ' . $between_condition . ' AND status=' . Lead::STATUS_SOLD . ')) AS ' . $category . ' ']);
        }

        if ($category == 'soldLeads') {
            $query->select(['e.id', 'e.username']);
            $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE (updated ' . $between_condition . ') AND employee_id=e.id AND status=' . Lead::STATUS_SOLD . ') AS ' . $category . ' ']);
        }

        if ($category == 'profitPerPax') {
            $query->select(['e.id', 'e.username']);
            $query->addSelect(['(SELECT 
            SUM(CASE
                    WHEN
                        employee_id = e.id AND lead_origin = 1
                    THEN
                    (final_profit - agents_processing_fee) / (adults + children)                    
                    ELSE 0
                END)
        FROM
            leads
                LEFT JOIN
            (SELECT 
                employee_id AS user_id,
                    id AS lead_id,
                    100 AS lead_ps,
                    TRUE AS lead_origin
            FROM
                leads
            WHERE               
                leads.id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')) AS unionleads ON id = lead_id
        WHERE
            (updated ' . $between_condition . ' AND status=10)) / (SELECT COUNT(*) FROM leads WHERE employee_id = e.id AND updated ' . $between_condition . ' AND status=' . Lead::STATUS_SOLD . ' ) AS ' . $category . ' ']);
        }

        if ($category == 'tips') {
            $query->select(['e.id', 'e.username']);
            $query->addSelect(['(SELECT 
            SUM(CASE
                    WHEN
                        employee_id = e.id AND lead_origin = 1
                    THEN
                    tips / 2
                    WHEN
                        employee_id = e.id AND lead_origin = 0
                    THEN                  
                    -((tips / 2) * (lead_ps / 100)) 
                    WHEN 
                    user_id = e.id AND lead_origin = 0
                    THEN
                    (tips / 2) * (lead_ps / 100)
                    ELSE 0
                END)
        FROM
            leads
                LEFT JOIN
            (SELECT 
                employee_id AS user_id,
                    id AS lead_id,
                    100 AS lead_ps,
                    TRUE AS lead_origin
            FROM
                leads
            WHERE               
                leads.id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')
                UNION ALL                 
            SELECT 
                ts_user_id AS user_id,
                    ts_lead_id AS lead_id,
                    ts_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                tips_split
            WHERE
                ts_lead_id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')) AS unionleads ON id = lead_id
        WHERE
            (updated ' . $between_condition . ' AND status=' . Lead::STATUS_SOLD . ')) AS ' . $category . ' ']);
        }

        if ($category == 'leadConversion') {
            $query->select(['employee_id, 
                             (SUM(CASE WHEN status IN(' . Lead::STATUS_SOLD . ',' . Lead::STATUS_BOOKED . ') AND (updated ' . $between_condition . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) /
                             SUM(CASE WHEN status NOT IN(' . Lead::STATUS_REJECT . ', ' . Lead::STATUS_TRASH . ', ' . Lead::STATUS_SNOOZE . ') AND (updated ' . $between_condition . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END)) AS leadConversion,
                             SUM(CASE WHEN status IN(' . Lead::STATUS_SOLD . ',' . Lead::STATUS_BOOKED . ') AND (updated ' . $between_condition . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS leadsToProcessing,
                             SUM(CASE WHEN status NOT IN(' . Lead::STATUS_REJECT . ', ' . Lead::STATUS_TRASH . ', ' . Lead::STATUS_SNOOZE . ') AND (updated ' . $between_condition . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS leadsWithoutRTS,
                             (SELECT username FROM `employees` WHERE id=employee_id) as username
                             FROM leads']);
            $query->leftJoin('user_params', 'user_params.up_user_id = employee_id')
                ->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = employee_id')
                ->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->groupBy(['employee_id']);
        } else {
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id')
                ->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->from('employees AS e')->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id')
                ->andWhere(['in', 'auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
        }

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            'sort' => [
                'defaultOrder' => [$category => SORT_DESC],
                'attributes' => [
                    $category
                ]
            ],
            /*'pagination' => [
                'pageSize' => 50,
            ],*/
            'pagination' => false
        ];

        return $dataProvider = new SqlDataProvider($paramsData);
    }

    /**
     * @param string $category
     * @param string $period
     * @param array $skills
     * @return SqlDataProvider
     */
    public function searchTopTeams(string $category, string $period, array $skills): SqlDataProvider
    {
        switch ($period) {
            case 'currentWeek':
                $interval = ChartTools::getWeek('0 week');
                break;
            case 'lastWeek':
                $interval = ChartTools::getWeek('-1 week');
                break;
            case 'currentMonth':
                $interval = ChartTools::getCurrentMonth();
                break;
        }

        $skillsSettings = '';
        foreach ($skills as $skill => $value) {
            foreach (UserProfile::SKILL_TYPE_LIST as $id => $item) {
                if ($skill == strtolower($item) && $value == false) {
                    $skillsSettings .= "' ', ";
                } elseif ($skill == strtolower($item) && $value == true) {
                    $skillsSettings .= "'" . $id . "', ";
                }
            }
        }
        $skillsSettings = substr($skillsSettings, 0, -2);

        /**
         * @var $interval array
         */
        $start = date("Y-m-d H:i", $interval['start']);
        $end = date("Y-m-d H:i", $interval['end']);
        $between_condition = " BETWEEN '{$start}' AND '{$end}'";

        $query = new Query();

        if ($category == 'teamsProfit') {
            $query->addSelect(['ug_name', 'SUM((SELECT 
            SUM(CASE
                    WHEN
                        employee_id = e.id AND lead_origin = 1
                    THEN
                    final_profit - agents_processing_fee
                    WHEN
                        employee_id = e.id AND lead_origin = 0
                    THEN                  
                    -((final_profit - agents_processing_fee) * (lead_ps / 100)) 
                    WHEN 
                    user_id = e.id AND lead_origin = 0
                    THEN
                    (final_profit - agents_processing_fee) * (lead_ps / 100)
                    ELSE 0
                END)
        FROM
            leads
                LEFT JOIN
            (SELECT 
                employee_id AS user_id,
                    id AS lead_id,
                    100 AS lead_ps,
                    TRUE AS lead_origin
            FROM
                leads
            WHERE               
                leads.id IN (select id from leads where (updated ' . $between_condition . ') and status=' . Lead::STATUS_SOLD . ')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (select id from leads where (updated ' . $between_condition . ') and status=' . Lead::STATUS_SOLD . ')) AS unionleads ON id = lead_id
        WHERE
            (updated ' . $between_condition . ' and status=' . Lead::STATUS_SOLD . '))) as teamsProfit']);

            $query->from('employees e');
            $query->leftJoin('user_group_assign uga', 'ugs_user_id = e.id');
            $query->leftJoin('user_group ug', 'ug.ug_id = uga.ugs_group_id');
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id');
            $query->leftJoin('user_profile', 'user_profile.up_user_id = user_params.up_user_id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id');
            $query->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere("user_profile.up_skill NOT IN($skillsSettings) OR user_profile.up_skill <=> NULL");
            $query->andWhere('ug_name IS NOT NULL');
            $query->andWhere(['=', 'ug.ug_on_leaderboard', true]);
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsSoldLeads') {
            $query->addSelect(['ug_name', 'COUNT(leads.status=' . Lead::STATUS_SOLD . ') / COUNT(DISTINCT(user_group_assign.ugs_user_id)) as teamsSoldLeads']);
            $query->leftJoin('user_group_assign', 'user_group_assign.ugs_group_id = user_group.ug_id');
            $query->leftJoin('leads', 'leads.employee_id = user_group_assign.ugs_user_id AND leads.status=' . Lead::STATUS_SOLD . ' AND (updated ' . $between_condition . ')');
            $query->rightJoin('user_params', 'user_params.up_user_id = user_group_assign.ugs_user_id')
                ->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->leftJoin('user_profile', 'user_profile.up_user_id = user_group_assign.ugs_user_id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user_group_assign.ugs_user_id')
                ->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere("user_profile.up_skill NOT IN($skillsSettings) OR user_profile.up_skill <=> NULL");
            $query->andWhere(['=', 'ug_on_leaderboard', true]);
            $query->from('user_group');
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsProfitPerPax') {
            $query->addSelect(['ug_name', 'AVG((SELECT 
            SUM(CASE
                    WHEN
                        employee_id = e.id AND lead_origin = 1
                    THEN
                    (final_profit - agents_processing_fee) / (adults + children)                    
                    ELSE 0
                END)
        FROM
            leads
                LEFT JOIN
            (SELECT 
                employee_id AS user_id,
                    id AS lead_id,
                    100 AS lead_ps,
                    TRUE AS lead_origin
            FROM
                leads
            WHERE               
                leads.id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')) AS unionleads ON id = lead_id
        WHERE
            (updated ' . $between_condition . ' AND status=' . Lead::STATUS_SOLD . ')) / (SELECT COUNT(*) FROM leads WHERE employee_id = e.id AND (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ' )) AS teamsProfitPerPax']);

            $query->from('employees e');
            $query->leftJoin('user_group_assign uga', 'ugs_user_id = e.id');
            $query->leftJoin('user_group ug', 'ug.ug_id = uga.ugs_group_id');
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id');
            $query->leftJoin('user_profile', 'user_profile.up_user_id = user_params.up_user_id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id');
            $query->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere("user_profile.up_skill NOT IN($skillsSettings) OR user_profile.up_skill <=> NULL");
            $query->andWhere('ug_name IS NOT NULL');
            $query->andWhere(['=', 'ug.ug_on_leaderboard', true]);
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsProfitPerAgent') {
            $query->addSelect(['ug_name', 'AVG((SELECT 
            SUM(CASE
                    WHEN
                        employee_id = e.id AND lead_origin = 1
                    THEN
                    (final_profit - agents_processing_fee)                 
                    ELSE 0
                END)
        FROM
            leads
                LEFT JOIN
            (SELECT 
                employee_id AS user_id,
                    id AS lead_id,
                    100 AS lead_ps,
                    TRUE AS lead_origin
            FROM
                leads
            WHERE               
                leads.id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated ' . $between_condition . ') AND status=' . Lead::STATUS_SOLD . ')) AS unionleads ON id = lead_id
        WHERE
            (updated ' . $between_condition . ' AND status=' . Lead::STATUS_SOLD . '))) AS teamsProfitPerAgent']);

            $query->from('employees e');
            $query->leftJoin('user_group_assign uga', 'ugs_user_id = e.id');
            $query->leftJoin('user_group ug', 'ug.ug_id = uga.ugs_group_id');
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id');
            $query->leftJoin('user_profile', 'user_profile.up_user_id = user_params.up_user_id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id');
            $query->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere("user_profile.up_skill NOT IN($skillsSettings) OR user_profile.up_skill <=> NULL");
            $query->andWhere('ug_name IS NOT NULL');
            $query->andWhere(['=', 'ug.ug_on_leaderboard', true]);
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsConversion') {
            $query->addSelect(['ug_name,  
                             (SUM(CASE WHEN status IN(' . Lead::STATUS_SOLD . ',' . Lead::STATUS_BOOKED . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) /
                             SUM(CASE WHEN status NOT IN(' . Lead::STATUS_REJECT . ', ' . Lead::STATUS_TRASH . ', ' . Lead::STATUS_SNOOZE . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END))  as teamsConversion,
                             SUM(CASE WHEN status IN(' . Lead::STATUS_SOLD . ',' . Lead::STATUS_BOOKED . ') AND (updated ' . $between_condition . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS teamLeadsToProcessing,
                             SUM(CASE WHEN status NOT IN(' . Lead::STATUS_REJECT . ', ' . Lead::STATUS_TRASH . ', ' . Lead::STATUS_SNOOZE . ') AND (updated ' . $between_condition . ') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status=' . Lead::STATUS_PROCESSING . ' AND employee_id=lf_owner_id AND lf_from_status_id=' . Lead::STATUS_SNOOZE . ' OR lf_from_status_id=' . Lead::STATUS_PENDING . ' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS teamLeadsWithoutRTS
            ']);
            $query->leftJoin('user_group_assign', 'user_group_assign.ugs_group_id = user_group.ug_id');
            $query->leftJoin('leads', 'leads.employee_id = user_group_assign.ugs_user_id AND (updated ' . $between_condition . ')');
            $query->rightJoin('user_params', 'user_params.up_user_id = user_group_assign.ugs_user_id')
                ->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->leftJoin('user_profile', 'user_profile.up_user_id = user_group_assign.ugs_user_id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user_group_assign.ugs_user_id')
                ->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere("user_profile.up_skill NOT IN($skillsSettings) OR user_profile.up_skill <=> NULL");
            $query->andWhere(['=', 'ug_on_leaderboard', true]);
            $query->from('user_group');
            $query->groupBy('ug_name');
        }

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            'sort' => [
                'defaultOrder' => [$category => SORT_DESC],
                'attributes' => [
                    $category
                ]
            ],
            /*'pagination' => [
                'pageSize' => 50,
            ],*/
            'pagination' => false
        ];

        return $dataProvider = new SqlDataProvider($paramsData);
    }

    /**
     * @param $params
     * @param $user Employee
     * @return ArrayDataProvider
     * @throws \Exception
     */
    public function leadFlowReport($params, $user): ArrayDataProvider
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
            $differenceTimeToFrom  = "24:00";
        } else {
            if ((strtotime($this->timeTo) - strtotime($this->timeFrom)) <= 0) {
                $differenceTimeToFrom = sprintf("%02d:00", (strtotime("24:00") - strtotime(sprintf("%02d:00", abs((strtotime($this->timeTo) - strtotime($this->timeFrom))) / 3600))) / 3600);
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

        if ($this->lfOwnerId != null) {
            $queryByOwner = " AND lf.lf_owner_id = '{$this->lfOwnerId}'";
        } else {
            $queryByOwner = '';
        }

        if ($this->departmentId != null) {
            $userIdsByDepartment = UserDepartment::find()->select(['ud_user_id'])->where(['=', 'ud_dep_id', $this->departmentId])->asArray()->all();
            $employeesFromDep = "'" . implode("', '", array_map(function ($entry) {
                return $entry['ud_user_id'];
            }, $userIdsByDepartment)) . "'";
            $queryByDepartment = " AND lf.lf_owner_id in " . "(" . $employeesFromDep . ")";
        } else {
            $queryByDepartment = '';
        }

        if ($this->userGroupId != null) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->userGroupId])->asArray()->all();
            $employees = "'" . implode("', '", array_map(function ($entry) {
                return $entry['ugs_user_id'];
            }, $userIdsByGroup)) . "'";
            $queryByGroup = " AND lf.lf_owner_id in " . "(" . $employees . ")";
        } else {
            $queryByGroup = '';
        }

        if ($this->projectId != null) {
            $queryByProject = " AND ls.project_id = {$this->projectId}";
        } else {
            $queryByProject = '';
        }

        if ($this->createdType != null) {
            $queryByCreatedType = " AND ls.l_type_create = {$this->createdType}";
        } else {
            $queryByCreatedType = '';
        }

        $query = new Query();

        $query->select(['lf.lf_owner_id AS user_id, DATE(CONVERT_TZ(DATE_SUB(lf.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) as created_date, COUNT(*) as cnt, 
                
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND `lf_from_status_id` = ' . Lead::STATUS_PENDING . ' AND lfw.status = ' . Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType . ') AS newTotal,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = ' . Lead::STATUS_PENDING . ' AND lfw.status = ' . Lead::STATUS_PROCESSING . ' AND lf_description = "Take" ' . $queryByProject . $queryByCreatedType . ') AS inboxLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND `lf_from_status_id` = ' . Lead::STATUS_PENDING . ' AND lfw.status = ' . Lead::STATUS_PROCESSING . ' AND lf_description = "Call AutoCreated Lead" ' . $queryByProject . $queryByCreatedType . ') AS callLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND `lf_from_status_id` = ' . Lead::STATUS_PENDING . ' AND lfw.status = ' . Lead::STATUS_PROCESSING . ' AND lf_description = "Lead redial" ' . $queryByProject . $queryByCreatedType . ') AS redialLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND lf_from_status_id IS NULL AND lfw.status = ' . Lead::STATUS_PROCESSING . ' AND ls.clone_id IS NULL ' . $queryByProject . $queryByCreatedType . ') AS leadsCreated,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND lf_from_status_id IS NULL AND lfw.status = ' . Lead::STATUS_PROCESSING . '  AND lfw.lf_description <> "Manual create" AND ls.clone_id IS NOT NULL ' . $queryByProject . $queryByCreatedType . ') AS leadsCloned,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND `lf_from_status_id` = ' . Lead::STATUS_FOLLOW_UP . ' AND lfw.status =  ' . Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType . ') AS followUpTotal,              
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lfw.employee_id AND `lf_from_status_id` = ' . Lead::STATUS_PROCESSING . ' AND lfw.status =  ' . Lead::STATUS_FOLLOW_UP . $queryByProject . $queryByCreatedType . ') AS toFollowUp,                
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = ' . Lead::STATUS_FOLLOW_UP . ' AND lfw.status = ' . Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType . ') AS followUpLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = ' . Lead::STATUS_PROCESSING . ' AND lfw.status = ' . Lead::STATUS_TRASH . $queryByProject . $queryByCreatedType . ') AS trashLeads,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND lfw.status = ' . Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType . ') AS soldLeads,    
            (SELECT SUM(CASE WHEN ls.final_profit IS NOT NULL AND ls.final_profit > 0 THEN ls.final_profit ELSE 0 END) FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND lfw.status = ' . Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType . ') AS profit,    
            (SELECT SUM(CASE WHEN ls.tips IS NOT NULL THEN ls.tips ELSE 0 END) FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL ' . $timeSub . ' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("' . $differenceTimeToFrom . '") AND user_id = lf_owner_id AND lfw.status = ' . Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType . ') AS tips
                
            FROM lead_flow AS lf WHERE lf.created ' . $between_condition . ' AND lf.lf_owner_id IS NOT NULL ' . $queryByOwner . $queryByGroup . $queryByDepartment . '        
        ']);

        $query->groupBy(['created_date', 'lf.lf_owner_id']);

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $model) {
            if (
                $model['newTotal'] == 0 &&
                $model['inboxLeadsTaken'] == 0 &&
                $model['callLeadsTaken'] == 0 &&
                $model['redialLeadsTaken'] == 0 &&
                $model['leadsCreated'] == 0 &&
                $model['leadsCloned'] == 0 &&
                $model['followUpTotal'] == 0 &&
                $model['toFollowUp'] == 0 &&
                $model['followUpLeadsTaken'] == 0 &&
                $model['trashLeads'] == 0 &&
                $model['soldLeads'] == 0 &&
                $model['profit'] == 0 &&
                $model['tips'] == 0
            ) {
                unset($data[$key]);
            }
        }

        $paramsData = [
            'allModels' => $data,
            'sort' => [
                'defaultOrder' => [
                    'user_id' => SORT_ASC,
                    'created_date' => SORT_ASC
                ],
                'attributes' => [
                    'user_id',
                    'created_date',
                    'newTotal',
                    'inboxLeadsTaken',
                    'callLeadsTaken',
                    'redialLeadsTaken',
                    'leadsCreated',
                    'leadsCloned',
                    'followUpTotal',
                    'toFollowUp',
                    'followUpLeadsTaken',
                    'trashLeads',
                    'soldLeads',
                    'profit',
                    'tips'
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return $dataProvider = new ArrayDataProvider($paramsData);
    }

    /**
     * @param $params
     * @param $user Employee
     * @return ArrayDataProvider
     * @throws \Exception
     */
    public function leadFlowStats($params, $user): ArrayDataProvider
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

        if ($this->lfOwnerId != null) {
            $queryByOwner = " lf.lf_owner_id = '{$this->lfOwnerId}'";
        } else {
            $owners = "'" . implode("', '", array_map(function ($entry) {
                return $entry;
            }, EmployeeGroupAccess::getUsersIdsInCommonGroups(Auth::id()))) . "'";
            $queryByOwner = " lf.lf_owner_id in " . "(" . $owners . ")";
        }

        if ($this->departmentId != null) {
            $userIdsByDepartment = UserDepartment::find()->select(['ud_user_id'])->where(['=', 'ud_dep_id', $this->departmentId])->asArray()->all();
            $employeesFromDep = "'" . implode("', '", array_map(function ($entry) {
                return $entry['ud_user_id'];
            }, $userIdsByDepartment)) . "'";
            $queryByDepartment = "lf.lf_owner_id in " . "(" . $employeesFromDep . ")";
        } else {
            $queryByDepartment = '';
        }

        if ($this->userGroupId != null) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->userGroupId])->asArray()->all();
            $employees = "'" . implode("', '", array_map(function ($entry) {
                return $entry['ugs_user_id'];
            }, $userIdsByGroup)) . "'";
            $queryByGroup = " lf.lf_owner_id in " . "(" . $employees . ")";
        } else {
            $queryByGroup = '';
        }

        if ($this->projectId != null) {
            $queryByProject = "ls.project_id = {$this->projectId}";
        } else {
            $queryByProject = '';
        }

        if ($this->createdType != null) {
            $queryByCreatedType = "ls.l_type_create = {$this->createdType}";
        } else {
            $queryByCreatedType = '';
        }

        $query = new Query();

        /*$query->select(['user_id, SUM(newTotal) as newTotal, SUM(inboxLeadsTaken) as inboxLeadsTaken, SUM(callLeadsTaken) as callLeadsTaken,  SUM(redialLeadsTaken) as redialLeadsTaken, SUM(leadsCreated) as leadsCreated, SUM(leadsCloned) as leadsCloned, SUM(followUpTotal) as followUpTotal, SUM(toFollowUp) as toFollowUp, SUM(followUpLeadsTaken) as followUpLeadsTaken, SUM(trashLeads) as trashLeads, SUM(soldLeads) as soldLeads, SUM(profit) as profit, SUM(tips) as tips, GROUP_CONCAT(created_date SEPARATOR \' \') as created_date FROM

            (SELECT lf.lf_owner_id AS user_id, DATE(CONVERT_TZ(DATE_SUB(lf.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) as created_date, COUNT(*) as cnt,

                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '.Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType .') AS newTotal,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '. Lead::STATUS_PROCESSING .' AND lf_description = "Take" '. $queryByProject . $queryByCreatedType .') AS inboxLeadsTaken,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '. Lead::STATUS_PROCESSING .' AND lf_description = "Call AutoCreated Lead" '. $queryByProject . $queryByCreatedType .') AS callLeadsTaken,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '. Lead::STATUS_PROCESSING .' AND lf_description = "Lead redial" '. $queryByProject . $queryByCreatedType .') AS redialLeadsTaken,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND lf_from_status_id IS NULL AND lfw.status = '. Lead::STATUS_PROCESSING .' AND ls.clone_id IS NULL '. $queryByProject . $queryByCreatedType .') AS leadsCreated,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND lf_from_status_id IS NULL AND lfw.status = '. Lead::STATUS_PROCESSING .'  AND lfw.lf_description <> "Manual create" AND ls.clone_id IS NOT NULL '. $queryByProject . $queryByCreatedType .') AS leadsCloned,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_FOLLOW_UP .' AND lfw.status =  '. Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType .') AS followUpTotal,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lfw.employee_id AND `lf_from_status_id` = '. Lead::STATUS_PROCESSING .' AND lfw.status =  '. Lead::STATUS_FOLLOW_UP . $queryByProject . $queryByCreatedType .') AS toFollowUp,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = '. Lead::STATUS_FOLLOW_UP .' AND lfw.status = '. Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType .') AS followUpLeadsTaken,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = '.Lead::STATUS_PROCESSING.' AND lfw.status = '. Lead::STATUS_TRASH . $queryByProject . $queryByCreatedType .') AS trashLeads,
                (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND lfw.status = '. Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType .') AS soldLeads,
                (SELECT SUM(CASE WHEN ls.final_profit IS NOT NULL AND ls.final_profit > 0 THEN ls.final_profit ELSE 0 END) FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND lfw.status = '. Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType .') AS profit,
                (SELECT SUM(CASE WHEN ls.tips IS NOT NULL THEN ls.tips ELSE 0 END) FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) = created_date AND TIME(CONVERT_TZ(DATE_SUB(lfw.created, INTERVAL '.$timeSub.' Hour), "+00:00", "' . $utcOffsetDST . '")) <= TIME("'. $differenceTimeToFrom .'") AND user_id = lf_owner_id AND lfw.status = '. Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType .') AS tips

            FROM lead_flow AS lf WHERE lf.created ' .$between_condition. ' AND lf.lf_owner_id IS NOT NULL '. $queryByOwner . $queryByGroup . $queryByDepartment. ' GROUP BY lf.lf_owner_id,  created_date) AS tbl
        ']);

        $query->groupBy(['tbl.user_id']);*/

        $query->select(['lf_owner_id,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_PENDING . ' AND lf.status = ' . Lead::STATUS_PROCESSING . ', 1, 0)) newTotal,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_PENDING . ' AND lf.status = ' . Lead::STATUS_PROCESSING . ' AND lf.lf_description = "Take" AND lf.lf_owner_id = lf.employee_id, 1, 0)) inboxLeadsTaken,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_PENDING . ' AND lf.status = ' . Lead::STATUS_PROCESSING . ' AND lf.lf_description = "Call AutoCreated Lead", 1, 0)) callLeadsTaken,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_PENDING . ' AND lf.status = ' . Lead::STATUS_PROCESSING . ' AND lf.lf_description = "Lead redial", 1, 0)) redialLeadsTaken,
            SUM(IF(lf.lf_from_status_id IS NULL AND lf.status = ' . Lead::STATUS_PROCESSING . ' AND ls.clone_id IS NULL AND lf.lf_owner_id = lf.employee_id, 1, 0)) leadsCreated,
            SUM(IF(lf.lf_from_status_id IS NULL AND lf.status = ' . Lead::STATUS_PROCESSING . ' AND ls.clone_id IS NOT NULL AND lf.lf_owner_id = lf.employee_id AND lf.lf_description <> "Manual create", 1, 0)) leadsCloned,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_FOLLOW_UP . ' AND lf.status = ' . Lead::STATUS_PROCESSING . ', 1, 0)) followUpTotal,	
            (SELECT COUNT(*) FROM lead_flow lfw WHERE lfw.created ' . $between_condition . ' AND lfw.employee_id = lf.lf_owner_id AND lfw.lf_from_status_id = ' . Lead::STATUS_PROCESSING . ' AND lfw.status = ' . Lead::STATUS_FOLLOW_UP . ' ) toFollowUp,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_FOLLOW_UP . ' AND lf.status = ' . Lead::STATUS_PROCESSING . ' AND lf.lf_owner_id = lf.employee_id, 1, 0)) followUpLeadsTaken,
            SUM(IF(lf.lf_from_status_id = ' . Lead::STATUS_PROCESSING . ' AND lf.status = ' . Lead::STATUS_TRASH . ' AND lf.lf_owner_id = lf.employee_id, 1, 0)) trashLeads,
            SUM(IF(lf.status = ' . Lead::STATUS_SOLD . ', 1, 0)) soldLeads,
            SUM(IF(lf.status = ' . Lead::STATUS_SOLD . ' AND ls.final_profit IS NOT NULL AND ls.final_profit > 0, ls.final_profit, 0)) profit,
            SUM(IF(lf.status = ' . Lead::STATUS_SOLD . ' AND ls.tips IS NOT NULL AND ls.final_profit > 0, ls.tips, 0)) tips
        ']);

        $query->from(LeadFlow::tableName() . ' lf');
        $query->leftJoin(Lead::tableName() . ' ls', 'lf.lead_id = ls.id');
        $query->where('lf.created' . $between_condition);
        $query->andWhere('lf_owner_id IS NOT NULL');
        $query->andWhere($queryByOwner);
        $query->andWhere($queryByDepartment);
        $query->andWhere($queryByGroup);
        $query->andWhere($queryByProject);
        $query->andWhere($queryByCreatedType);
        $query->groupBy('lf_owner_id');

        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $model) {
            if (
                $model['newTotal'] == 0 &&
                $model['inboxLeadsTaken'] == 0 &&
                $model['callLeadsTaken'] == 0 &&
                $model['redialLeadsTaken'] == 0 &&
                $model['leadsCreated'] == 0 &&
                $model['leadsCloned'] == 0 &&
                $model['followUpTotal'] == 0 &&
                $model['toFollowUp'] == 0 &&
                $model['followUpLeadsTaken'] == 0 &&
                $model['trashLeads'] == 0 &&
                $model['soldLeads'] == 0 &&
                $model['profit'] == 0 &&
                $model['tips'] == 0
            ) {
                unset($data[$key]);
            }
        }

        $paramsData = [
            'allModels' => $data,
            'sort' => [
                'defaultOrder' => [
                    'user_id' => SORT_ASC,
                ],
                'attributes' => [
                    'user_id',
                    'newTotal',
                    'inboxLeadsTaken',
                    'callLeadsTaken',
                    'redialLeadsTaken',
                    'leadsCreated',
                    'leadsCloned',
                    'followUpTotal',
                    'toFollowUp',
                    'followUpLeadsTaken',
                    'trashLeads',
                    'soldLeads',
                    'profit',
                    'tips'
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return $dataProvider = new ArrayDataProvider($paramsData);
    }

    public function searchUserLeadsInfo($params, $userID)
    {
        $this->load($params);

        $query = new Query();
        $query->addSelect(['DATE(created) as createdDate,
            COUNT(id) AS allUserLeads,
            SUM(IF(status = ' . Lead::STATUS_BOOKED . ', 1, 0)) AS bookedLeads
        ']);
        $query->from(static::tableName());
        $query->where(['employee_id' => $userID]);
        if ($this->datetime_start && $this->datetime_end) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }
        $query->groupBy('createdDate');

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            'sort' => [
                'defaultOrder' => [
                    'createdDate' => SORT_DESC,
                ],
                'attributes' => [
                    'createdDate',
                    'allUserLeads',
                    'bookedLeads',
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ];

        return new SqlDataProvider($paramsData);
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchExtraQueue($params, Employee $user): ActiveDataProvider
    {
        $query = $this->leadBadgesRepository->getExtraQueueQuery();
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);
        $leadTable = Lead::tableName();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.source_id' => $this->source_id,
            $leadTable . '.client_id' => $this->client_id,
            $leadTable . '.cabin' => $this->cabin,
            $leadTable . '.request_ip' => $this->request_ip,
            $leadTable . '.l_init_price' => $this->l_init_price,
            $leadTable . '.l_call_status_id' => $this->l_call_status_id,
            $leadTable . '.l_type' => $this->l_type,
        ]);

        if ($this->email_status > 0) {
            if ((int) $this->email_status === 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if ($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SENT, Quote::STATUS_OPENED] ]);
            if ((int) $this->quote_status === 2) {
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') > 0'));
            } else {
                $query->andWhere(new Expression('(' . $subQuery->createCommand()->getRawSql() . ') = 0'));
            }
        }

        if ($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0] && $departRange[1]) {
                $having[] = "MAX(departure) >= '" . date('Y-m-d', strtotime($departRange[0])) . "'";
                $having[] = "MIN(departure) <= '" . date('Y-m-d', strtotime($departRange[1])) . "'";
                $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
                $query->andWhere(['IN', 'leads.id', $subQuery]);
            }
        }

        if (!empty($this->origin_airport)) {
            $query->innerJoin([
                'segment_origin_airport' => LeadFlightSegment
                    ::find()
                    ->select(['lead_id'])
                    ->where(['origin' => $this->origin_airport])
                    ->groupBy(['lead_id'])
            ], 'leads.id = segment_origin_airport.lead_id');
        }

        if (!empty($this->destination_airport)) {
            $query->innerJoin([
                'segment_destination_airport' => LeadFlightSegment
                    ::find()
                    ->select(['lead_id'])
                    ->where(['destination' => $this->destination_airport])
                    ->groupBy(['lead_id'])
            ], 'leads.id = segment_destination_airport.lead_id');
        }


        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->l_last_action_dt) {
            $query->andFilterWhere(['>=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt))])
                ->andFilterWhere(['<=', 'l_last_action_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->l_last_action_dt) + 3600 * 24)]);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);

        return $dataProvider;
    }
}
