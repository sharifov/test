<?php

namespace common\models\search;

use common\models\Airport;
use common\models\Call;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Email;
use common\models\Employee;
use common\models\Sms;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use Faker\Provider\DateTime;
use sales\access\EmployeeProjectAccess;
use sales\repositories\lead\LeadBadgesRepository;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lead;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use common\models\Quote;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\ProfitSplit;
use common\models\TipsSplit;
use common\components\ChartTools;
use yii\helpers\VarDumper;

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
    public $soldRangeTime;
    public $createTimeRange;
    public $createdType;

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

    private $leadBadgesRepository;

    public function __construct($config = [])
    {
        $this->leadBadgesRepository = new LeadBadgesRepository();
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
            [['id', 'client_id', 'employee_id', 'status', 'project_id', 'adults', 'children', 'infants', 'rating', 'called_expert', 'cnt', 'l_answered', 'supervision_id', 'limit', 'bo_flight_id', 'l_duplicate_lead_id', 'l_type_create'], 'integer'],
            [['email_status', 'quote_status', 'l_is_test'], 'integer'],
            [['lfOwnerId', 'userGroupId', 'departmentId', 'projectId', 'createdType'], 'integer'],

            [['client_name', 'client_email', 'client_phone','quote_pnr', 'gid', 'origin_airport','destination_airport', 'origin_country', 'destination_country', 'l_request_hash'], 'string'],

            //['created_date_from', 'default', 'value' => '2018-01-01'],
            //['created_date_to', 'default', 'value' => date('Y-m-d')],

            [['uid', 'trip_type', 'cabin', 'notes_for_experts', 'created', 'updated', 'request_ip', 'request_ip_detail', 'offset_gmt', 'snooze_for', 'discount_id',
                'created_date_from', 'created_date_to', 'depart_date_from', 'depart_date_to', 'source_id', 'statuses', 'sold_date_from', 'sold_date_to', 'processing_filter', 'l_init_price', 'l_last_action_dt'], 'safe'],
            ['l_init_price', 'filter', 'filter' => function($value) {
                return $value ? filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
            }],

            ['last_ticket_date', 'safe'],
            [['departRangeTime', 'createdRangeTime', 'soldRangeTime', 'updatedRangeTime', 'lastActionRangeTime'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],

            ['remainingDays', 'integer'],
            ['remainingDays', 'filter', 'filter' => static function($value) {
                return (int)$value;
            }, 'skipOnEmpty' => true],
			['l_is_test', 'in', 'range' => [0,1]],
            ['l_call_status_id', 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    /*public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels2 = [
            'statuses' => 'Statuses',
            'created_date_from' => 'Created date from',
            'created_date_to' => 'Created date to',
        ];

        $labels = array_merge($labels, $labels2);

        return $labels;
    }*/

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
    public function search($params)
    {
        $query = Lead::find()->with('project', 'source', 'employee', 'client');
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
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
                'desc' => [Lead::tableName() .'.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() .'.id' => SORT_DESC]
            ]
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
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
			'l_is_test'		=> $this->l_is_test
        ]);

        if($this->statuses) {
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

        if($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '".date('Y-m-d', strtotime($departRange[0]))."'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '".date('Y-m-d', strtotime($departRange[1]))."'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->soldRangeTime) {
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

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if($this->quote_pnr) {
            //$subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            //$query->andWhere(['IN', 'leads.id', $subQuery]);

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"'.$this->quote_pnr.'"%\'')]);
        }

        if($this->supervision_id > 0) {

            if(
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

                if($this->statuses && in_array(Lead::STATUS_FOLLOW_UP, $this->statuses) && count($this->statuses) == 1) {

                } elseif($this->statuses && in_array(Lead::STATUS_PENDING, $this->statuses) && count($this->statuses) == 1){

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

        if(!empty($this->origin_airport)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if(!empty($this->destination_airport)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if(!empty($this->origin_country)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.countryId',$this->origin_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.countryId',$this->origin_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }
        if(!empty($this->destination_country)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.countryId',$this->destination_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.countryId',$this->destination_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }
        /*  $sqlRaw = $query->createCommand()->getRawSql();

        VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchExport($params): ActiveDataProvider
    {
        $query = Lead::find()->with('project', 'source', 'employee', 'client');
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
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
                'desc' => [Lead::tableName() .'.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() .'.id' => SORT_DESC]
            ]
        ]);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
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

        if($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }

        if($this->createdType) {
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

        if($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '".date('Y-m-d', strtotime($departRange[0]))."'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '".date('Y-m-d', strtotime($departRange[1]))."'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->soldRangeTime) {
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

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if($this->quote_pnr) {
            //$subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            //$query->andWhere(['IN', 'leads.id', $subQuery]);

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"'.$this->quote_pnr.'"%\'')]);
        }

        if($this->supervision_id > 0) {

            if(
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

                if($this->statuses && in_array(Lead::STATUS_FOLLOW_UP, $this->statuses) && count($this->statuses) == 1) {

                } elseif($this->statuses && in_array(Lead::STATUS_PENDING, $this->statuses) && count($this->statuses) == 1){

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

        if(!empty($this->origin_airport)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','origin',$this->origin_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if(!empty($this->destination_airport)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->andFilterWhere(['like','destination',$this->destination_airport])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }

        if(!empty($this->origin_country)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.countryId',$this->origin_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.origin')
                ->andFilterWhere(['like','airports.countryId',$this->origin_country])
                ->andWhere(['IN','id', $subQuery1]);

            $query->andWhere(['IN', 'leads.id', $subQuery2]);
        }
        if(!empty($this->destination_country)){
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.countryId',$this->destination_country]);

            $subQuery1 = LeadFlightSegment::find()->select(['MIN(id)'])->where(['IN','lead_id', $subQuery])->groupBy('lead_id');

            $subQuery2 = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->leftJoin('airports','airports.iata = lead_flight_segments.destination')
                ->andFilterWhere(['like','airports.countryId',$this->destination_country])
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
                ->select(['sum('.Call::tableName() . '.c_recording_duration)'])
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
                ->select(['sum('.Call::tableName() . '.c_recording_duration)'])
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

    public function searchAgent($params)
    {
        $projectIds = array_keys(EmployeeProjectAccess::getProjects());
        $query = Lead::find();
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
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

        $query->andWhere(['IN', Lead::tableName() . '.project_id', $projectIds]);

        /*'id' => ''
        'uid' => ''
        'client_id' => ''
        'client_name' => ''
        'client_email' => ''
        'client_phone' => ''
        'bo_flight_id' => ''
        'employee_id' => ''*/

        if($this->id || $this->uid || $this->gid || $this->client_id || $this->client_name || $this->client_email || $this->client_phone || $this->bo_flight_id || $this->employee_id || $this->request_ip) {

        } else {
            $this->employee_id = Yii::$app->user->id;
        }

        //VarDumper::dump($params, 10, true); exit;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
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
            'l_answered'    => $this->l_answered
        ]);

        if($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }
        $query->andWhere(['<>', 'status', Lead::STATUS_PENDING]);

        if($this->created_date_from || $this->created_date_to) {

            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }

        } else {

            if($this->created) {
                $query->andFilterWhere(['DATE(created)'=> date('Y-m-d', strtotime($this->created))]);
            }
        }

        if($this->departRangeTime) {
            $departRange = explode(" - ", $this->departRangeTime);
            $having = [];
            if ($departRange[0]) {
                $having[] = "MIN(departure) >= '".date('Y-m-d', strtotime($departRange[0]))."'";
            }
            if ($departRange[1]) {
                $having[] = "MIN(departure) <= '".date('Y-m-d', strtotime($departRange[1]))."'";
            }
            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['=', 'clients.last_name', $this->client_name])
                        ->orWhere(['=', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            if ($this->client_phone) {
                $this->client_phone = (strpos($this->client_phone, '+') === 0 ? '+' : '') . str_replace('+', '',
                        $this->client_phone);
            }

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['phone' => $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->quote_pnr) {
            /* $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]); */

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"'.$this->quote_pnr.'"%\'')]);
        }

        /*  $sqlRaw = $query->createCommand()->getRawSql();
         VarDumper::dump($sqlRaw, 10, true); exit; */

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
            'sort'=> [
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
                'desc' => [Lead::tableName() .'.id' => SORT_DESC]
            ],
            'id' => [
                'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                'desc' => [Lead::tableName() .'.id' => SORT_DESC]
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

        if(
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


        if($this->statuses) {
            $query->andWhere([Lead::tableName() . '.status' => $this->statuses]);
        }
        $query->andWhere(['<>', Lead::tableName() . '.status', Lead::STATUS_PENDING]);

        if($this->created_date_from || $this->created_date_to) {

            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }

        } else {

            if($this->created) {
                $query->andFilterWhere(['DATE(created)'=> date('Y-m-d', strtotime($this->created))]);
            }
        }

        if($this->depart_date_from || $this->depart_date_to) {
            $having = [];
            if ($this->depart_date_from) {
                $having[] = "MIN(departure) >= '".date('Y-m-d', strtotime($this->depart_date_from))."'";
            }
            if ($this->depart_date_to) {
                $having[] = "MIN(departure) <= '".date('Y-m-d', strtotime($this->depart_date_to))."'";
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

        if($this->client_name) {
            $subQuery = Client::find()->select(['clients.id'])->distinct('clients.id')->where(['=', 'clients.last_name', $this->client_name])
                ->orWhere(['=', 'clients.first_name', $this->client_name]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['=', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->quote_pnr) {
            /* $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]); */

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"'.$this->quote_pnr.'"%\'')]);
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
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find()->with('project', 'source');

        $query = $this->leadBadgesRepository->getSoldQuery($user)->with('project', 'source', 'employee')->joinWith('leadFlowSold' );
        $this->load($params);
        $leadTable = Lead::tableName();

        $query->select([$leadTable . '.*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        // $query->leftJoin(Quote::tableName(), [Quote::tableName() . '.lead_id' => new Expression('leads.id')])->where([Quote::tableName() . '.status' => Quote::STATUS_APPLIED]);

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
                ],
                'defaultOrder' => ['id' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable.'.id' => $this->id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.employee_id' => $this->employee_id,
        ]);

//        $query
//        ->andWhere(['leads.status' => Lead::STATUS_SOLD])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds]);

        if ($this->updated) {
            $query->andFilterWhere(['=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->updated))]);
        }

        if ($this->last_ticket_date) {
//            $query->andWhere(['=', 'DATE(' . Quote::tableName() . '.last_ticket_date)', date('Y-m-d', strtotime($this->last_ticket_date))]);
            $subQuery = LeadFlow::find()->select(['lead_flow.lead_id'])->distinct('lead_flow.lead_id')->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');
            $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_ticket_date))])
                ->andFilterWhere(['<=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_ticket_date) + 3600 *24)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->sold_date_from || $this->sold_date_to) {

            $subQuery = LeadFlow::find()->select(['lead_flow.lead_id'])->distinct('lead_flow.lead_id')->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($this->sold_date_from) {
                $subQuery->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->sold_date_from))]);
            }
            if ($this->sold_date_to) {
                $subQuery->andFilterWhere(['<', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($this->sold_date_to))]);
            }
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

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
            'sort'=> ['defaultOrder' => ['updated' => SORT_DESC]],
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.rating' => $this->rating,
        ]);

        $query
            ->andWhere(['leads.status' => Lead::STATUS_SOLD]);
        //->andWhere(['IN', $leadTable . '.project_id', $projectIds])

        if(!empty($this->updated)){
            $query->andFilterWhere(['=','DATE(leads.updated)', date('Y-m-d', strtotime($this->updated))]);
        }

        if($this->sold_date_from || $this->sold_date_to) {
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
        if($this->quote_pnr) {
            $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        if($this->employee_id){
            $query
                ->leftJoin(ProfitSplit::tableName().' ps','ps.ps_lead_id = leads.id')
                ->leftJoin(TipsSplit::tableName().' ts','ts.ts_lead_id = leads.id')
                ->andWhere($leadTable.'.employee_id = '. $this->employee_id.' OR ps.ps_user_id ='.$this->employee_id.' OR ts.ts_user_id ='.$this->employee_id)
                ->groupBy(['leads.id']);
        }else{
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
            'sort'=> ['defaultOrder' => ['updated' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable.'.id' => $this->id,
            $leadTable.'.uid' => $this->uid,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.employee_id' => $this->employee_id,
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
    public function searchProcessing($params, Employee $user): ActiveDataProvider
    {
//        $projectIds = array_keys(EmployeeAccess::getProjects());
//        $query = Lead::find()->with('project');
        $query = $this->leadBadgesRepository->getProcessingQuery($user)->with('project');
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['l_last_action_dt' => SORT_DESC],'attributes' => ['id','updated','created','status', 'l_last_action_dt']],
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
        ]);

//        $query->andWhere(['IN','leads.status', [self::STATUS_SNOOZE, self::STATUS_PROCESSING, self::STATUS_ON_HOLD]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds]);

        if($this->email_status > 0) {
            if($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SEND, Quote::STATUS_OPENED] ]);
            if($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') > 0'));
            } else {
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') = 0'));
            }
        }

//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'leadChecklists', 'leadChecklists.lcType', 'employee']);

        /*  $sqlRaw = $query->createCommand()->getRawSql();
         VarDumper::dump($sqlRaw, 10, true); exit; */

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
            ->from(['a' => Airport::tableName()])
            ->andWhere('a.iata = (' .
                (new Query())
                    ->select(['lfs.origin'])
                    ->from(['lfs' => LeadFlightSegment::tableName()])
                    ->andWhere('lfs.lead_id = ' . $leadTable . '.id')
                    ->orderBy(['lfs.departure' => SORT_ASC])
                    ->limit(1)
                    ->createCommand()->getSql()
                . ')'
            )
            ->createCommand()->getSql();

        $query->addSelect([
            'remainingDays' =>
                new Expression("datediff((" . $departureQuery . "), (date(convert_tz(NOW(), '+00:00', (" . $nowQuery . ")))))")
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
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
            // $query->where('0=1');
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.l_answered' => $this->l_answered,
            $leadTable.'.project_id' => $this->project_id,
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

        if($this->email_status > 0) {
            if($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 0'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SEND, Quote::STATUS_OPENED] ]);
            if($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') > 0'));
            } else {
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') = 0'));
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
            'leadFlightSegments' => static function(ActiveQuery $query) {
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
//            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SEND, Quote::STATUS_OPENED] ]);
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
            // $query->where('0=1');
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
            'sort'=> [
                'defaultOrder' => ['updated' => SORT_DESC],
                'attributes' => [
                    'id',
                    'project_id',
                    'created',
                    'updated'
                ]
            ],
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
        ]);

//        $query
//        ->andWhere(['IN','leads.status', [self::STATUS_TRASH]])
//        ->andWhere(['IN', $leadTable . '.project_id', $projectIds]);


//        if($this->supervision_id > 0) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
//        }

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
            'sort'=> ['defaultOrder' => ['created' => SORT_DESC]],
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

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable . '.id' => $this->id,
            $leadTable . '.l_duplicate_lead_id' => $this->l_duplicate_lead_id,
            $leadTable . '.l_request_hash' => $this->l_request_hash,
            $leadTable . '.project_id' => $this->project_id,
            $leadTable . '.employee_id' => $this->employee_id,
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

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee']);

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

        if($this->client_email) {
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

        if($this->client_phone) {
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

        if($this->request_ip) {
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

        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_SOLD.') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_ON_HOLD.') AS st_on_hold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_PROCESSING.') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_FOLLOW_UP.') AS st_follow_up ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_TRASH.') AS st_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_REJECT.') AS st_reject ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_BOOKED.') AS st_booked ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_SNOOZE.') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_PENDING.') AS st_pending ']);
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
    public function searchTopAgents(string $category, string $period):SqlDataProvider
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

        if ($category == 'finalProfit'){
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
                leads.id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')) AS unionleads ON id = lead_id
        WHERE
            (updated '.$between_condition.' AND status='.Lead::STATUS_SOLD.')) AS '.$category.' ']);
        }

        if ($category == 'soldLeads'){
            $query->select(['e.id', 'e.username']);
            $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE (updated '.$between_condition.') AND employee_id=e.id AND status='.Lead::STATUS_SOLD.') AS '.$category.' ']);
        }

        if ($category == 'profitPerPax'){
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
                leads.id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')) AS unionleads ON id = lead_id
        WHERE
            (updated '.$between_condition.' AND status=10)) / (SELECT COUNT(*) FROM leads WHERE employee_id = e.id AND updated '.$between_condition.' AND status='.Lead::STATUS_SOLD.' ) AS '.$category.' ']);
        }

        if ($category == 'tips'){
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
                leads.id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')
                UNION ALL                 
            SELECT 
                ts_user_id AS user_id,
                    ts_lead_id AS lead_id,
                    ts_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                tips_split
            WHERE
                ts_lead_id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')) AS unionleads ON id = lead_id
        WHERE
            (updated '.$between_condition.' AND status='.Lead::STATUS_SOLD.')) AS '.$category.' ']);
        }

        if ($category == 'leadConversion'){
            $query->select(['employee_id, 
                             (SUM(CASE WHEN status IN('.Lead::STATUS_SOLD.','.Lead::STATUS_BOOKED.') AND (updated '.$between_condition.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) /
                             SUM(CASE WHEN status NOT IN('.Lead::STATUS_REJECT.', '.Lead::STATUS_TRASH.', '.Lead::STATUS_SNOOZE.') AND (updated '.$between_condition.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END)) AS leadConversion,
                             SUM(CASE WHEN status IN('.Lead::STATUS_SOLD.','.Lead::STATUS_BOOKED.') AND (updated '.$between_condition.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS leadsToProcessing,
                             SUM(CASE WHEN status NOT IN('.Lead::STATUS_REJECT.', '.Lead::STATUS_TRASH.', '.Lead::STATUS_SNOOZE.') AND (updated '.$between_condition.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS leadsWithoutRTS,
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
            'sort' =>[
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
     * @return SqlDataProvider
     */
    public function searchTopTeams(string $category, string $period):SqlDataProvider
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

        if ($category == 'teamsProfit'){
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
                leads.id IN (select id from leads where (updated '.$between_condition.') and status='.Lead::STATUS_SOLD.')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (select id from leads where (updated '.$between_condition.') and status='.Lead::STATUS_SOLD.')) AS unionleads ON id = lead_id
        WHERE
            (updated '.$between_condition.' and status='.Lead::STATUS_SOLD.'))) as teamsProfit']);

            $query->from('employees e' );
            $query->leftJoin('user_group_assign uga', 'ugs_user_id = e.id');
            $query->leftJoin('user_group ug', 'ug.ug_id = uga.ugs_group_id');
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id');
            $query->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere('ug_name IS NOT NULL');
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsSoldLeads'){
            $query->addSelect(['ug_name', 'COUNT(leads.status='.Lead::STATUS_SOLD.') / COUNT(DISTINCT(user_group_assign.ugs_user_id)) as teamsSoldLeads']);
            $query->leftJoin('user_group_assign', 'user_group_assign.ugs_group_id = user_group.ug_id');
            $query->leftJoin('leads', 'leads.employee_id = user_group_assign.ugs_user_id AND leads.status='.Lead::STATUS_SOLD.' AND (updated '.$between_condition.')');
            $query->rightJoin('user_params', 'user_params.up_user_id = user_group_assign.ugs_user_id')
                ->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user_group_assign.ugs_user_id')
                ->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->from('user_group' );
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsProfitPerPax'){
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
                leads.id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')) AS unionleads ON id = lead_id
        WHERE
            (updated '.$between_condition.' AND status='.Lead::STATUS_SOLD.')) / (SELECT COUNT(*) FROM leads WHERE employee_id = e.id AND (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.' )) AS teamsProfitPerPax']);

            $query->from('employees e' );
            $query->leftJoin('user_group_assign uga', 'ugs_user_id = e.id');
            $query->leftJoin('user_group ug', 'ug.ug_id = uga.ugs_group_id');
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id');
            $query->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere('ug_name IS NOT NULL');
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsProfitPerAgent'){
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
                leads.id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')
                UNION ALL                 
            SELECT 
                ps_user_id AS user_id,
                    ps_lead_id AS lead_id,
                    ps_percent AS lead_ps,
                    FALSE AS lead_origin
            FROM
                profit_split
            WHERE
                ps_lead_id IN (SELECT id FROM leads WHERE (updated '.$between_condition.') AND status='.Lead::STATUS_SOLD.')) AS unionleads ON id = lead_id
        WHERE
            (updated '.$between_condition.' AND status='.Lead::STATUS_SOLD.'))) AS teamsProfitPerAgent']);

            $query->from('employees e' );
            $query->leftJoin('user_group_assign uga', 'ugs_user_id = e.id');
            $query->leftJoin('user_group ug', 'ug.ug_id = uga.ugs_group_id');
            $query->leftJoin('user_params', 'user_params.up_user_id = e.id');
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = e.id');
            $query->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->andWhere('ug_name IS NOT NULL');
            $query->groupBy('ug_name');
        }

        if ($category == 'teamsConversion'){
            $query->addSelect(['ug_name,  
                             (SUM(CASE WHEN status IN('.Lead::STATUS_SOLD.','.Lead::STATUS_BOOKED.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) /
                             SUM(CASE WHEN status NOT IN('.Lead::STATUS_REJECT.', '.Lead::STATUS_TRASH.', '.Lead::STATUS_SNOOZE.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END))  as teamsConversion,
                             SUM(CASE WHEN status IN('.Lead::STATUS_SOLD.','.Lead::STATUS_BOOKED.') AND (updated '.$between_condition.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS teamLeadsToProcessing,
                             SUM(CASE WHEN status NOT IN('.Lead::STATUS_REJECT.', '.Lead::STATUS_TRASH.', '.Lead::STATUS_SNOOZE.') AND (updated '.$between_condition.') AND employee_id IS NOT NULL AND status IS NOT NULL AND id in (SELECT lead_id as id FROM lead_flow WHERE status='.Lead::STATUS_PROCESSING.' AND employee_id=lf_owner_id AND lf_from_status_id='.Lead::STATUS_SNOOZE.' OR lf_from_status_id='.Lead::STATUS_PENDING.' AND employee_id IS NOT NULL AND lf_owner_id IS NOT NULL) THEN 1 ELSE 0 END) AS teamLeadsWithoutRTS
            ']);
            $query->leftJoin('user_group_assign', 'user_group_assign.ugs_group_id = user_group.ug_id');
            $query->leftJoin('leads', 'leads.employee_id = user_group_assign.ugs_user_id AND (updated '.$between_condition.')');
            $query->rightJoin('user_params', 'user_params.up_user_id = user_group_assign.ugs_user_id')
                ->andWhere(['=', 'user_params.up_leaderboard_enabled', true]);
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user_group_assign.ugs_user_id')
                ->andWhere(['in','auth_assignment.item_name', [Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]]);
            $query->from('user_group' );
            $query->groupBy('ug_name');
        }

        $command = $query->createCommand();
        $sql = $command->rawSql;
        $paramsData = [
            'sql' => $sql,
            'sort' =>[
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
     * @return SqlDataProvider
     * @throws \Exception
     */
    public function leadFlowReport($params, $user):SqlDataProvider
    {
        $this->load($params);
        $timezone = $user->timezone;
        $userTZ = Employee::timezoneList(false)[$timezone] ?? date('P');

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

        if($this->lfOwnerId != null) {
            $queryByOwner = " AND lf.lf_owner_id = '{$this->lfOwnerId}'";
        } else {
            $queryByOwner = '';
        }

        if ($this->departmentId != null) {
            $userIdsByDepartment = UserDepartment::find()->select(['ud_user_id'])->where(['=', 'ud_dep_id', $this->departmentId])->asArray()->all();
            $employeesFromDep = "'" . implode("', '", array_map(function ($entry) {
                    return $entry['ud_user_id'];
                }, $userIdsByDepartment)) . "'";
            $queryByDepartment = " AND lf.lf_owner_id in " . "(" . $employeesFromDep .")";
        } else {
            $queryByDepartment = '';
        }

        if($this->userGroupId != null) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->userGroupId])->asArray()->all();
            $employees = "'" . implode("', '", array_map(function ($entry) {
                    return $entry['ugs_user_id'];
                }, $userIdsByGroup)) . "'";
            $queryByGroup = " AND lf.lf_owner_id in " . "(" . $employees .")";
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

        $query->select(['lf.lf_owner_id AS user_id, DATE(CONVERT_TZ(DATE_SUB(lf.created, INTERVAL '.$hourSub.' Hour), "+00:00", "' . $userTZ . '")) as created_date, COUNT(*) as cnt,
                
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '.Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType .') AS newTotal,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '. Lead::STATUS_PROCESSING .' AND lf_description = "Take" '. $queryByProject . $queryByCreatedType .') AS inboxLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '. Lead::STATUS_PROCESSING .' AND lf_description = "Call AutoCreated Lead" '. $queryByProject . $queryByCreatedType .') AS callLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PENDING .' AND lfw.status = '. Lead::STATUS_PROCESSING .' AND lf_description = "Lead redial" '. $queryByProject . $queryByCreatedType .') AS redialLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND user_id = lfw.employee_id AND lf_from_status_id IS NULL AND lfw.status = '. Lead::STATUS_PROCESSING .' AND ls.clone_id IS NULL '. $queryByProject . $queryByCreatedType .') AS leadsCreated,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND user_id = lfw.employee_id AND lf_from_status_id IS NULL AND lfw.status = '. Lead::STATUS_PROCESSING .'  AND lfw.lf_description <> "Manual create" AND ls.clone_id IS NOT NULL '. $queryByProject . $queryByCreatedType .') AS leadsCloned,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_FOLLOW_UP .' AND lfw.status =  '. Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType .') AS followUpTotal,              
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lfw.employee_id AND `lf_from_status_id` = '. Lead::STATUS_PROCESSING .' AND lfw.status =  '. Lead::STATUS_FOLLOW_UP . $queryByProject . $queryByCreatedType .') AS toFollowUp,                
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = '. Lead::STATUS_FOLLOW_UP .' AND lfw.status = '. Lead::STATUS_PROCESSING . $queryByProject . $queryByCreatedType .') AS followUpLeadsTaken,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND user_id = lfw.employee_id AND `lf_from_status_id` = '.Lead::STATUS_PROCESSING.' AND lfw.status = '. Lead::STATUS_TRASH . $queryByProject . $queryByCreatedType .') AS trashLeads,    
            (SELECT COUNT(*) AS cnt FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND `lf_from_status_id` = '. Lead::STATUS_PROCESSING .' AND lfw.status = '. Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType .') AS soldLeads,    
            (SELECT SUM(CASE WHEN ls.final_profit IS NOT NULL AND ls.final_profit > 0 THEN ls.final_profit ELSE 0 END) FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND lf_from_status_id = '. Lead::STATUS_PROCESSING .' AND lfw.status = '. Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType .') AS profit,    
            (SELECT SUM(CASE WHEN ls.tips IS NOT NULL THEN ls.tips ELSE 0 END) FROM lead_flow lfw LEFT JOIN leads ls ON lfw.lead_id = ls.id WHERE DATE(lfw.created) = created_date AND user_id = lf_owner_id AND lf_from_status_id = '. Lead::STATUS_PROCESSING .' AND lfw.status = '. Lead::STATUS_SOLD . $queryByProject . $queryByCreatedType .') AS tips
                
            FROM lead_flow AS lf WHERE lf.created ' .$between_condition. ' AND lf.lf_owner_id IS NOT NULL '. $queryByOwner . $queryByGroup . $queryByDepartment. '        
        ']);

        $query->groupBy(['DATE(CONVERT_TZ(DATE_SUB(lf.created, INTERVAL '.$hourSub.' Hour), "+00:00", "' . $userTZ . '")), lf.lf_owner_id']);
        //$query->orderBy(['user_id' => SORT_ASC, 'created_date' => SORT_ASC]);

        $command = $query->createCommand();
        $sql = $command->sql;

        $paramsData = [
            'sql' => $sql,
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

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }
}
