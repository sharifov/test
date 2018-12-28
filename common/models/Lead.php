<?php

namespace common\models;

use common\components\EmailService;
use common\models\local\LeadAdditionalInformation;
use common\models\local\LeadLogMessage;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use common\models\local\FlightSegment;
use common\components\SearchService;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
 * @property int $client_id
 * @property int $employee_id
 * @property int $status
 * @property string $uid
 * @property int $project_id
 * @property int $source_id
 * @property string $trip_type
 * @property string $cabin
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property string $notes_for_experts
 * @property string $request_ip
 * @property string $offset_gmt
 * @property string $request_ip_detail
 * @property int $rating
 * @property string $created
 * @property string $updated
 * @property string $snooze_for
 * @property boolean $called_expert
 * @property string $discount_id
 * @property string $bo_flight_id
 * @property string $additional_information
 * @property int $l_answered
 * @property int $l_grade
 * @property int $clone_id
 * @property string $description
 * @property double $final_profit
 * @property double $tips
 *
 * @property LeadFlightSegment[] $leadFlightSegments
 * @property LeadFlow[] $leadFlows
 * @property LeadLog[] $leadLogs
 * @property LeadPreferences $leadPreferences
 * @property Client $client
 * @property Employee $employee
 * @property Source $source
 * @property Project $project
 * @property int $quotesCount
 * @property int $leadFlightSegmentsCount
 * @property LeadAdditionalInformation[] $additionalInformationForm
 * @property Lead $clone
 * @property ProfitSplit[] $profitSplits
 * @property TipsSplit[] $tipsSplits
 *
 */
class Lead extends ActiveRecord
{
    public CONST
        TRIP_TYPE_ONE_WAY = 'OW',
        TRIP_TYPE_ROUND_TRIP = 'RT',
        TRIP_TYPE_MULTI_DESTINATION = 'MC';
    public CONST TRIP_TYPE_LIST = [
        self::TRIP_TYPE_ROUND_TRIP => 'Round Trip',
        self::TRIP_TYPE_ONE_WAY => 'One Way',
        self::TRIP_TYPE_MULTI_DESTINATION => 'Multidestination'
    ];
    public CONST
        STATUS_PENDING = 1,
        STATUS_PROCESSING = 2,
        STATUS_REJECT = 4,
        STATUS_FOLLOW_UP = 5,
        STATUS_ON_HOLD = 8,
        STATUS_SOLD = 10,
        STATUS_TRASH = 11,
        STATUS_BOOKED = 12,
        STATUS_SNOOZE = 13;

    public CONST STATUS_LIST = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_REJECT => 'Reject',
        self::STATUS_FOLLOW_UP => 'Follow Up',
        self::STATUS_ON_HOLD => 'Hold On',
        self::STATUS_SOLD => 'Sold',
        self::STATUS_TRASH => 'Trash',
        self::STATUS_BOOKED => 'Booked',
        self::STATUS_SNOOZE => 'Snooze',
    ];

    public CONST CLONE_REASONS = [
        1 => 'Group travel',
        2 => 'Alternative credit card',
        3 => 'Different flight',
        4 => 'Flight adjustments',
        0 => 'Other',
    ];

    public CONST STATUS_MULTIPLE_UPDATE_LIST = [
        self::STATUS_FOLLOW_UP => self::STATUS_LIST[self::STATUS_FOLLOW_UP],
        self::STATUS_ON_HOLD => self::STATUS_LIST[self::STATUS_ON_HOLD],
        self::STATUS_PROCESSING => self::STATUS_LIST[self::STATUS_PROCESSING],
        self::STATUS_TRASH => self::STATUS_LIST[self::STATUS_TRASH],
        self::STATUS_BOOKED => self::STATUS_LIST[self::STATUS_BOOKED],
        self::STATUS_SNOOZE => self::STATUS_LIST[self::STATUS_SNOOZE],
    ];

    public CONST STATUS_CLASS_LIST = [
        self::STATUS_PENDING => 'll-pending',
        self::STATUS_PROCESSING => 'll-processing',
        self::STATUS_FOLLOW_UP => 'll-follow_up',
        self::STATUS_ON_HOLD => 'll-on_hold',
        self::STATUS_SOLD => 'll-sold',
        self::STATUS_TRASH => 'll-trash',
        self::STATUS_BOOKED => 'll-booked',
        self::STATUS_SNOOZE => 'll-snooze',
    ];
    public CONST
        CABIN_ECONOMY = 'E',
        CABIN_BUSINESS = 'B',
        CABIN_FIRST = 'F',
        CABIN_PREMIUM = 'P';
    public CONST CABIN_LIST = [
        self::CABIN_ECONOMY => 'Economy',
        self::CABIN_PREMIUM => 'Premium eco',
        self::CABIN_BUSINESS => 'Business',
        self::CABIN_FIRST => 'First',
    ];
    public CONST
        DIV_GRID_WITH_OUT_EMAIL = 1,
        DIV_GRID_WITH_EMAIL = 2,
        DIV_GRID_SEND_QUOTES = 3,
        DIV_GRID_IN_SNOOZE = 4;

    public CONST SCENARIO_API = 'scenario_api';
    public CONST SCENARIO_MULTIPLE_UPDATE = 'scenario_multiple_update';

    public $additionalInformationForm;
    public $status_description;
    public $totalProfit;
    public $splitProfitPercentSum = 0;
    public $totalTips;
    public $splitTipsPercentSum = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leads';
    }

    public function init()
    {
        parent::init();
        $this->additionalInformationForm = [new LeadAdditionalInformation()];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['trip_type', 'cabin'], 'required'],
            [['adults', 'children', 'infants', 'source_id'], 'required'], //'except' => self::SCENARIO_API],

            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'rating', 'l_grade'], 'integer'],
            [['adults', 'children', 'infants'], 'integer', 'max' => 9],
            [['adults'], 'integer', 'min' => 1],

            [['l_answered'], 'boolean'],

            [['notes_for_experts'], 'string'],
            [['created', 'updated', 'offset_gmt', 'request_ip', 'request_ip_detail', 'snooze_for',
                'called_expert', 'discount_id', 'bo_flight_id', 'additional_information', 'final_profit', 'tips'], 'safe'],
            [['uid'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],
            [['status_description'], 'string'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'status' => 'Status',
            'uid' => 'Uid',
            'project_id' => 'Project ID',
            'source_id' => 'Source ID',
            'trip_type' => 'Trip Type',
            'cabin' => 'Cabin',
            'adults' => 'Adults',
            'children' => 'Children',
            'infants' => 'Infants',
            'notes_for_experts' => 'Notes for Expert',
            'created' => 'Created',
            'updated' => 'Updated',
            'l_answered' => 'Answered',
            'l_grade' => 'Grade',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlows()
    {
        return $this->hasMany(LeadFlow::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadLogs()
    {
        return $this->hasMany(LeadLog::class, ['lead_id' => 'id']);
    }


    public static function getDivs($div = null)
    {
        $mapping = [
            self::DIV_GRID_IN_SNOOZE => 'Leads in snooze',
            self::DIV_GRID_WITH_OUT_EMAIL => 'Leads with out email',
            self::DIV_GRID_WITH_EMAIL => 'Leads with email',
            self::DIV_GRID_SEND_QUOTES => 'Leads with send quotes'
        ];
        if ($div === null) {
            return $mapping;
        } else {
            return $mapping[$div];
        }
    }

    /**
     * @return array|null
     */
    public static function getBadges()
    {
        $badges = array_flip(self::getLeadQueueType());
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());

        $userId = Yii::$app->user->id;

        foreach ($badges as $key => $value) {
            $status = [];
            switch ($key) {
                case 'inbox':
                    $status[] = self::STATUS_PENDING;
                    break;
                case 'follow-up':
                    $status[] = self::STATUS_FOLLOW_UP;
                    break;
                case 'booked':
                    $status[] = self::STATUS_BOOKED;
                    break;
                case 'sold':
                    $status[] = self::STATUS_SOLD;
                    break;
                case 'trash':
                    $status[] = self::STATUS_TRASH;
                    break;
                default:
                    $status = [
                        self::STATUS_PROCESSING, self::STATUS_ON_HOLD,
                        self::STATUS_SNOOZE
                    ];
                    break;
            }

            $query = self::find()
                ->where(['IN', self::tableName() . '.status', $status])
                ->andWhere(['IN', self::tableName() . '.project_id', $projectIds]);

            if ((Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) && in_array($key, ['trash', 'sold', 'follow-up', 'booked'])) {
                $query->andWhere(['=', 'created', date('Y-m-d')]);
            }

            if (Yii::$app->user->identity->role == 'agent' && in_array($key, ['trash'])) {
                $badges[$key] = 0;
                continue;
            }

            if (Yii::$app->user->identity->role == 'agent' && in_array($key, ['sold'])) {
                $query->andWhere([
                    'employee_id' => $userId
                ]);
            }

            if (in_array($key, ['processing'])) {
                $query->andWhere([
                    'employee_id' => $userId
                ]);
            }

            $query->select(['COUNT(id) as cntd'])->limit(1);

            $badges[$key] = $query->scalar();
        }

        return $badges;
    }

    public static function getBadgesSingleQuery()
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());

        $userId = Yii::$app->user->id;
        $created = '';
        $employee = '';
        if (Yii::$app->authManager->getAssignment('agent', $userId)) {
            $employee = ' AND employee_id = ' . $userId;
        }

        $sold = '';

        if (Yii::$app->authManager->getAssignment('supervision', $userId)) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $userId]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $resEmp = $subQuery->createCommand()->queryAll();
            $empArr = [];
            if ($resEmp) {
                foreach ($resEmp as $entry) {
                    $empArr[] = $entry['ugs_user_id'];
                }
            }

            if (!empty($empArr)) {
                $employee = 'AND leads.employee_id IN (' . implode(',', $empArr) . ')';
            }

        }

        if (Yii::$app->user->identity->role == 'agent') {
            $sold = $employee;
        }
        $default = implode(',', [
            self::STATUS_PROCESSING,
            self::STATUS_ON_HOLD,
            self::STATUS_SNOOZE
        ]);

        $select = [
            'inbox' => 'SUM(CASE WHEN status IN (:inbox) THEN 1 ELSE 0 END)',
            'follow-up' => 'SUM(CASE WHEN status IN (:followup) ' . $created . ' THEN 1 ELSE 0 END)',
            'booked' => 'SUM(CASE WHEN status IN (:booked) ' . $created . $employee . ' THEN 1 ELSE 0 END)',
            'sold' => 'SUM(CASE WHEN status IN (:sold) ' . $created . $sold . $employee . ' THEN 1 ELSE 0 END)',
            'processing' => 'SUM(CASE WHEN status IN (' . $default . ') ' . $employee . ' THEN 1 ELSE 0 END)'];

        if (Yii::$app->user->identity->role != 'agent') {
            $select['trash'] = 'SUM(CASE WHEN status IN (' . self::STATUS_TRASH . ') ' . $created . $employee . ' THEN 1 ELSE 0 END)';
        }

        $query = self::find()
            ->select($select)
            ->andWhere(['IN', 'project_id', $projectIds])
            ->addParams([':inbox' => self::STATUS_PENDING,
                ':followup' => self::STATUS_FOLLOW_UP,
                ':booked' => self::STATUS_BOOKED,
                ':sold' => self::STATUS_SOLD,
            ])
            ->limit(1);


        //echo $query->createCommand()->getRawSql();die;

        return $query->createCommand()->queryOne();
    }

    public static function getLeadQueueType()
    {
        return [
            'inbox', 'follow-up', 'processing',
            'processing-all', 'booked', 'sold', 'trash'
        ];
    }

    public static function search($queue, $searchModel = null, $divGridBy = null)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $status = [];
        switch ($queue) {
            case 'inbox':
                $status[] = self::STATUS_PENDING;
                break;
            case 'follow-up':
                $status[] = self::STATUS_FOLLOW_UP;
                break;
            case 'booked':
                $status[] = self::STATUS_BOOKED;
                break;
            case 'sold':
                $status[] = self::STATUS_SOLD;
                break;
            case 'trash':
                $status[] = self::STATUS_TRASH;
                break;
            default:
                if ($divGridBy === self::DIV_GRID_IN_SNOOZE) {
                    $status[] = self::STATUS_SNOOZE;
                } else {
                    $status = [self::STATUS_PROCESSING, self::STATUS_ON_HOLD];
                }
                break;
        }


        $lastActivityNoteQuery = new Query();
        $lastActivityNoteQuery->select([
            'MAX(' . Note::tableName() . '.created) AS last_activity',
            Note::tableName() . '.lead_id'
        ])->from(Note::tableName())
            ->innerJoin(Lead::tableName(), Lead::tableName() . '.`id` = ' . Note::tableName() . '.`lead_id`')
            ->where(Lead::tableName() . '.`status` IN (' . implode(',', $status) . ')')
            ->groupBy(' lead_id');

        $lastActivityLeadQuery = new Query();
        $lastActivityLeadQuery->select([
            Lead::tableName() . '.updated AS last_activity',
            Lead::tableName() . '.id AS lead_id'
        ])->from(Lead::tableName())
            ->where(Lead::tableName() . '.`status` IN (' . implode(',', $status) . ')');

        $lastActivityTable = sprintf('(SELECT MAX(last_activity) AS last_activity, lead_id FROM(%s UNION %s) AS lastActivityUnion GROUP BY `lead_id`)  AS lastActivityTable',
            $lastActivityNoteQuery->createCommand()->rawSql,
            $lastActivityLeadQuery->createCommand()->rawSql
        );

        //var_dump($lastActivityTable);

        $selected = [
            Lead::tableName() . '.id', Lead::tableName() . '.bo_flight_id',
            Lead::tableName() . '.adults', Lead::tableName() . '.children',
            Lead::tableName() . '.infants', Lead::tableName() . '.cabin',
            Lead::tableName() . '.status', Lead::tableName() . '.employee_id',
            Lead::tableName() . '.rating', Lead::tableName() . '.source_id',
            Lead::tableName() . '.additional_information', Source::tableName() . '.name',
            LeadFlightSegment::tableName() . '.destination', Employee::tableName() . '.username',
            LeadFlightSegment::tableName() . '.departure', Lead::tableName() . '.updated AS updated',
            Lead::tableName() . '.created', Client::tableName() . '.first_name', Client::tableName() . '.last_name', 'lastActivityTable.last_activity AS last_activity',
            Airport::tableName() . '.city', Reason::tableName() . '.reason', Lead::tableName() . '.snooze_for', Lead::tableName() . '.l_grade', Lead::tableName() . '.l_answered',
            'g_ce.emails', 'g_cp.phones', 'all_q.send_q', 'all_q.not_send_q', 'g_detail_lfs.flight_detail'
        ];

        $query = new Query();
        $query->from(Lead::tableName())
            ->innerJoin(Source::tableName(), Source::tableName() . '.id = ' . Lead::tableName() . '.source_id')
            ->innerJoin(LeadFlightSegment::tableName(), LeadFlightSegment::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
            ->innerJoin('(' . (new Query)
                    ->select(['lead_id', 'MIN(id) as first_fs'])
                    ->from(LeadFlightSegment::tableName())
                    ->groupBy('lead_id')
                    ->createCommand()->rawSql . ') as g_lfs',
                'g_lfs.lead_id = ' . LeadFlightSegment::tableName() . '.lead_id AND g_lfs.first_fs = ' . LeadFlightSegment::tableName() . '.id'
            )
            ->leftJoin(Airport::tableName(), Airport::tableName() . '.iata = ' . LeadFlightSegment::tableName() . '.destination')
            ->leftJoin('(' . (new Query())
                    ->select(['lead_id', 'MAX(id) as last_reason'])
                    ->from(Reason::tableName())
                    ->groupBy('lead_id')
                    ->createCommand()->rawSql . ') as g_reason',
                'g_reason.lead_id = ' . Lead::tableName() . '.id'
            )
            ->leftJoin(Reason::tableName(), Reason::tableName() . '.id = g_reason.last_reason')
            ->leftJoin($lastActivityTable,
                'lastActivityTable.lead_id = ' . Lead::tableName() . '.id')
            ->innerJoin('(' . (new Query)
                    ->select(['lead_id', 'GROUP_CONCAT(CONCAT(departure, \' \', origin, \'-\', destination) SEPARATOR \'<br>\') AS flight_detail'])
                    ->from(LeadFlightSegment::tableName())
                    ->groupBy('lead_id')
                    ->createCommand()->rawSql . ') as g_detail_lfs',
                'g_detail_lfs.lead_id = ' . Lead::tableName() . '.id'
            )
            ->innerJoin(Client::tableName(), Client::tableName() . '.id = ' . Lead::tableName() . '.client_id')
            ->leftJoin('(' . (new Query)
                    ->select(['GROUP_CONCAT(email SEPARATOR \'<br>\') AS emails', 'client_id'])
                    ->from(ClientEmail::tableName())
                    ->groupBy('client_id')
                    ->createCommand()->rawSql . ') as g_ce',
                'g_ce.client_id = ' . Client::tableName() . '.id'
            )
            ->leftJoin('(' . (new Query)
                    ->select(['GROUP_CONCAT(phone SEPARATOR \'<br>\') AS phones', 'client_id'])
                    ->from(ClientPhone::tableName())
                    ->groupBy('client_id')
                    ->createCommand()->rawSql . ') as g_cp',
                'g_cp.client_id = ' . Client::tableName() . '.id'
            )
            ->leftJoin('(' . (new Query)
                    ->select([
                        'lead_id',
                        'SUM(CASE WHEN status IN (2, 4, 5) THEN 1 ELSE 0 END) AS send_q',
                        'SUM(CASE WHEN status NOT IN (2, 4, 5) THEN 1 ELSE 0 END) AS not_send_q',
                    ])
                    ->from(Quote::tableName())
                    ->groupBy('lead_id')
                    ->createCommand()->rawSql . ') as all_q',
                'all_q.lead_id = ' . Lead::tableName() . '.id'
            )
            ->leftJoin(Employee::tableName(), Employee::tableName() . '.id = ' . Lead::tableName() . '.employee_id');

        if (in_array($queue, ['sold', 'booked'])) {
            $selected[] = Quote::tableName() . '.reservation_dump';
            $selected[] = Quote::tableName() . '.check_payment';
            $selected[] = Quote::tableName() . '.fare_type';
            $selected[] = 'g_qp.selling';
            $selected[] = 'g_qp.mark_up';

            $query->leftJoin(Quote::tableName(), Quote::tableName() . '.lead_id = ' . Lead::tableName() . '.id AND ' . Quote::tableName() . '.status = :status', [
                ':status' => Quote::STATUS_APPLIED
            ]);
            $query->leftJoin('(' . (new Query)
                    ->select(['quote_id', 'SUM(selling) as selling', 'SUM(mark_up + extra_mark_up) as mark_up'])
                    ->from(QuotePrice::tableName())
                    ->groupBy('quote_id')
                    ->createCommand()->rawSql . ') as g_qp',
                'g_qp.quote_id = ' . Quote::tableName() . '.id'
            );
        }

        $query->select($selected);

        $query->where(['IN', Lead::tableName() . '.status', $status])
            ->andWhere(['IN', Lead::tableName() . '.project_id', $projectIds]);


        if (Yii::$app->user->identity->role == 'agent' && in_array($queue, ['sold'])) {
            $query->andWhere([
                Lead::tableName() . '.employee_id' => Yii::$app->user->identity->getId()
            ]);
        }

        if ($searchModel !== null && in_array($queue, ['processing-all', 'trash'])) {
            $query->andFilterWhere([
                Lead::tableName() . '.employee_id' => $searchModel->employee_id
            ]);
        }

        if ($divGridBy !== null) {
            switch ($divGridBy) {
                case self::DIV_GRID_WITH_OUT_EMAIL:
                    $query->andWhere('g_ce.emails IS NULL');
                    break;
                case self::DIV_GRID_WITH_EMAIL:
                    $subQuery = new Query();
                    $subQuery->select(['lead_id'])->from(Quote::tableName())->where(['IN', 'status', [
                        Quote::STATUS_SEND,
                        Quote::STATUS_OPENED,
                        Quote::STATUS_APPLIED
                    ]]);
                    $query->andWhere('g_ce.emails IS NOT NULL');
                    $query->andWhere(['NOT IN', Lead::tableName() . '.id', ArrayHelper::map($subQuery->all(), 'lead_id', 'lead_id')]);
                    break;
                case self::DIV_GRID_SEND_QUOTES:
                    $subQuery = new Query();
                    $subQuery->select(['lead_id'])->from(Quote::tableName())->where(['IN', 'status', [
                        Quote::STATUS_SEND,
                        Quote::STATUS_OPENED,
                        Quote::STATUS_APPLIED
                    ]]);
                    $query->andWhere(['IN', Lead::tableName() . '.id', ArrayHelper::map($subQuery->all(), 'lead_id', 'lead_id')]);
                    break;
            }
        }

        if (in_array($queue, ['follow-up'])) {
            $showAll = Yii::$app->request->cookies->getValue(self::getCookiesKey(), true);
            if (!$showAll) {
                $query->andWhere([
                    'NOT IN', Lead::tableName() . '.id', self::unprocessedByAgentInFollowUp()
                ]);
            }
        }

        if (in_array($queue, ['processing'])) {
            $query->andWhere([
                Lead::tableName() . '.employee_id' => Yii::$app->user->identity->getId()
            ]);
        }

        //$query->distinct = true;

        //var_dump($query->createCommand()->rawSql);
        //var_dump($query->count());
        //die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);

        if ($queue != 'trash') {
            $dataProvider->sort->defaultOrder = !in_array($queue, ['sold', 'booked'])
                ? ['pending' => SORT_DESC]
                : ['pending_last_status' => SORT_DESC];
        } else {
            $dataProvider->sort->defaultOrder = ['pending_in_trash' => SORT_DESC];
            $dataProvider->sort->attributes['pending_in_trash'] = [
                'asc' => [Lead::tableName() . '.updated' => SORT_ASC],
                'desc' => [Lead::tableName() . '.updated' => SORT_DESC],
            ];
        }
        $dataProvider->sort->attributes['last_activity'] = [
            'asc' => ['lastActivityTable.last_activity' => SORT_ASC],
            'desc' => ['lastActivityTable.last_activity' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['pending'] = [
            'asc' => [Lead::tableName() . '.created' => SORT_ASC],
            'desc' => [Lead::tableName() . '.created' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['pending_last_status'] = [
            'asc' => [Lead::tableName() . '.updated' => SORT_ASC],
            'desc' => [Lead::tableName() . '.updated' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['rating'] = [
            'asc' => [Lead::tableName() . '.rating' => SORT_ASC],
            'desc' => [Lead::tableName() . '.rating' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['source_id'] = [
            'asc' => [Lead::tableName() . '.source_id' => SORT_DESC],
            'desc' => [Lead::tableName() . '.source_id' => SORT_ASC],
        ];

        return $dataProvider;
    }

    public static function getCookiesKey()
    {
        return sprintf('sale-unprocessed-followup-%d', Yii::$app->user->identity->getId());
    }

    public static function unprocessedByAgentInFollowUp()
    {
        $subQuery = (new Query())
            ->select(['lead_id'])->from(LeadFlow::tableName())
            ->where([
                'employee_id' => Yii::$app->user->identity->getId(),
                'status' => self::STATUS_FOLLOW_UP
            ]);
        $subQuery->distinct = true;
        return ArrayHelper::map($subQuery->all(), 'lead_id', 'lead_id');
    }

    public static function getCabin($cabin = null)
    {
        $mapping = self::CABIN_LIST;

        if ($cabin === null) {
            return $mapping;
        }

        return isset($mapping[$cabin]) ? $mapping[$cabin] : $cabin;
    }

    public static function getRating($id, $rating)
    {
        $checked1 = $checked2 = $checked3 = '';
        if ($rating == 3) {
            $checked3 = 'checked';
        } elseif ($rating == 2) {
            $checked2 = 'checked';
        } elseif ($rating == 1) {
            $checked1 = 'checked';
        }

        return '<fieldset class="rate-input-group">
                    <input type="radio" name="rate-' . $id . '" id="rate-3-' . $id . '" value="3" ' . $checked3 . ' disabled>
                    <label for="rate-3-' . $id . '"></label>

                    <input type="radio" name="rate-' . $id . '" id="rate-2-' . $id . '" value="2" ' . $checked2 . ' disabled>
                    <label for="rate-2-' . $id . '"></label>

                    <input type="radio" name="rate-' . $id . '" id="rate-1-' . $id . '" value="1" ' . $checked1 . ' disabled>
                    <label for="rate-1-' . $id . '"></label>
                </fieldset>';
    }

    /**
     * @param int $value
     * @return string
     */
    public static function getRating2($value = 0): string
    {
        $str = '';

        if ($value > 0) {
            for ($i = 1; $i <= $value; $i++) {
                $str .= '<i class="fa fa-star "></i> ';
            }

            $str .= ' (' . $value . ')';

            switch ($value) {
                case 1:
                    $class = 'text-danger';
                    break;
                case 2:
                    $class = 'text-warning';
                    break;
                case 3:
                    $class = 'text-success';
                    break;
                default:
                    $class = '';
            }

            $str = '<div class="' . $class . '">' . $str . '</div>';
        } else {
            $str = '-';
        }

        return $str;
    }

    public static function getSnoozeCountdown($id, $snooze_for)
    {
        if (!empty($snooze_for)) {
            return self::getCountdownTimer(new \DateTime($snooze_for), sprintf('snooze-countdown-%d', $id));
        }
        return '-';
    }

    private function getCountdownTimer(\DateTime $expired, $spanId)
    {
        return '<span id="' . $spanId . '" data-toggle="tooltip" data-placement="right" data-original-title="' . $expired->format('Y-m-d H:i') . '"></span>
                <script type="text/javascript">
                    var expired = moment.tz("' . $expired->format('Y-m-d H:i:s') . '", "UTC");
                    $("#' . $spanId . '").countdown(expired.toDate(), function(event) {
                        if (event.elapsed == false) {
                            $(this).text(
                                event.strftime(\'%Dd %Hh %Mm\')
                            );
                        } else {
                            $(this).text(
                                event.strftime(\'On Wake\')
                            ).addClass(\'text-success\');
                        }
                    });
                </script>';
    }

    public static function getLastActivity($updated)
    {
        $now = new \DateTime();
        $lastUpdate = new \DateTime($updated);
        /*if (!empty($note_created)) {
            $created = new \DateTime($note_created);
            return ($lastUpdate->getTimestamp() > $created->getTimestamp())
                ? self::diffFormat($now->diff($lastUpdate))
                : self::diffFormat($now->diff($created));
        } else {*/
        return self::diffFormat($now->diff($lastUpdate));
        //}
    }

    protected function diffFormat(\DateInterval $interval)
    {
        $return = [];

        if ($interval->format('%y') > 0) {
            $return[] = $interval->format('%y') . 'y';
        }
        if ($interval->format('%m') > 0) {
            $return[] = $interval->format('%m') . 'mh';
        }
        if ($interval->format('%d') > 0) {
            $return[] = $interval->format('%d') . 'd';
        }
        if ($interval->format('%i') >= 0 && $interval->format('%h') >= 0) {
            $return[] = $interval->format('%h') . 'h ' . $interval->format('%I') . 'm';
        }

        return implode(' ', $return);
    }


    public function permissionsView()
    {
        if (Yii::$app->user->identity->role != 'admin') {
            $access = ProjectEmployeeAccess::findOne([
                'employee_id' => Yii::$app->user->id,
                'project_id' => $this->project_id
            ]);
            return ($access !== null);
        } else {
            return true;
        }
    }

    public function getFlowTransition()
    {
        return LeadFlow::findAll(['lead_id' => $this->id]);
    }

    public function getPendingAfterCreate($created = null)
    {
        $now = new \DateTime();
        if (empty($created)) {
            $created = $this->created;
        }
        $created = new \DateTime($created);
        return self::diffFormat($now->diff($created));
    }

    public function getPendingInLastStatus($updated)
    {
        $now = new \DateTime();
        $updated = new \DateTime($updated);
        return self::diffFormat($now->diff($updated));
    }

    public function getStatusLabel($status = null)
    {
        $label = '';
        $status = empty($status) ? $this->status : $status;
        switch ($status) {
            case self::STATUS_PENDING:
                $label = '<span class="label status-label bg-light-brown">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_SNOOZE:
            case self::STATUS_PROCESSING:
                $label = '<span class="label status-label bg-turquoise">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_ON_HOLD:
            case self::STATUS_FOLLOW_UP:
                $label = '<span class="label status-label bg-blue">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_SOLD:
            case self::STATUS_BOOKED:
                $label = '<span class="label status-label bg-green">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_TRASH:
            case self::STATUS_REJECT:
                $label = '<span class="label status-label bg-red">' . self::getStatus($status) . '</span>';
                break;
        }
        return $label;
    }

    /**
     * @param $status_id
     * @return string
     */
    public static function getStatus($status_id): string
    {
        return self::STATUS_LIST[$status_id] ?? '-';
    }

    /**
     * @param bool $label
     * @return string
     */
    public function getStatusName(bool $label = false): string
    {
        $statusName = self::STATUS_LIST[$this->status] ?? '-';

        if ($label) {
            $class = $this->getStatusLabelClass();
            $statusName = '<span class="label ' . $class . '" style="font-size: 13px">' . Html::encode($statusName) . '</span>';
        }

        return $statusName;
    }

    /**
     * @return string
     */
    public function getStatusLabelClass(): string
    {
        return self::STATUS_CLASS_LIST[$this->status] ?? 'label-default';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClone()
    {
        return $this->hasOne(Lead::className(), ['id' => 'clone_id']);
    }

    /**
     * @return bool
     */
    public function updateIpInfo()
    {

        if (empty($this->offset_gmt) && !empty($this->request_ip)) {

            $ctx = stream_context_create(['http' =>
                ['timeout' => 5]  //Seconds
            ]);

            try {
                $jsonData = file_get_contents(Yii::$app->params['checkIpURL'] . $this->request_ip, false, $ctx);
            } catch (\Throwable $throwable) {
                return false;
            }

            if ($jsonData) {

                $data = @json_decode($jsonData, true);

                //print_r($data); exit;

                if (isset($data['meta']['code']) && $data['meta']['code'] == '200') {
                    if (isset($data['data']['datetime'])) {
                        $this->offset_gmt = str_replace(':', '.', $data['data']['datetime']['offset_gmt']);
                    }
                    $this->request_ip_detail = json_encode($data['data']);
                    //$this->update(false, ['offset_gmt', 'request_ip_detail']);

                    Lead::updateAll(['offset_gmt' => $this->offset_gmt, 'request_ip_detail' => $this->request_ip_detail], ['id' => $this->id]);

                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @param null $type
     * @param null $employee_id
     * @param null $employee2_id
     * @param  $lead
     * @return bool
     */
    public function sendNotification($type = null, $employee_id = null, $employee2_id = null, $lead = null)
    {
        $isSend = false;

        $host = \Yii::$app->params['url_address'];

        if ($type && $employee_id && isset(Yii::$app->params['email_from']['sales'])) {
            $user = Employee::findOne($employee_id);
            $user2 = Employee::findOne($employee2_id);

            if ($user && $user->email) {

                $swiftMailer = Yii::$app->mailer2;

                $userName = $user->username;

                if ($user2) {
                    $userName2 = $user2->username;
                } else {
                    $userName2 = '-';
                }

                $body = 'Hi!';
                $subject = '[Sales] Default subject';

                if ($type === 'reassigned-lead') {

                    $body = Yii::t('email', "Dear {name},
Attention!
Your Lead (ID: {lead_id}) has been reassigned to another agent ({name2}).

You can view lead here: {url}

Regards,
Sales - Kivork",
                        [
                            'name' => $userName,
                            'name2' => $userName2,
                            'url' => $host . '/lead/booked/' . $this->id,
                            'lead_id' => $this->id,
                            'br' => "\r\n"
                        ]);

                    $subject = Yii::t('email', "⚠ [Sales] Your Lead-{id} has been reassigned to another agent ({username})", ['id' => $this->id, 'username' => $userName2]);

                } elseif ($type === 'lead-status-sold') {

                    $quote = Quote::find()->where(['lead_id' => $this->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();
                    $flightSegment = LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['id' => SORT_ASC])->one();
                    $airlineName = '-';
                    $profit = 0;
                    if (!empty($quote)) {
                        $airline = Airline::findOne(['iata' => $quote->main_airline_code]);
                        if (!empty($airline)) {
                            $airlineName = $airline->name;
                        }
                        $profit = number_format(Quote::countProfit($quote->id), 2);
                    }

                    $body = Yii::t('email', "
Booked quote with UID : {quote_uid},
Source: {name},
Sale ID: {lead_id} (Link to lead {url})
{name} made \${profit} on {airline} to {destination}

Regards,
Sales - Kivork",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/booked/' . $this->id,
                            'lead_id' => $this->id,
                            'quote_uid' => $quote ? $quote->uid : '-',
                            'destination' => $flightSegment ? $flightSegment->destination : '-',
                            'airline' => $airlineName,
                            'profit' => $profit,
                            'br' => "\r\n"
                        ]);

                    $subject = Yii::t('email', "❀ [Sales] Your Lead-{id} has been changed status to SOLD", ['id' => $this->id]);
                } elseif ($type === 'lead-status-booked') {


                    $subject = Yii::t('email', "⚐ [Sales] Your Lead-{id} has been changed status to BOOKED", ['id' => $this->id]);
                    $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

                    $body = Yii::t('email', "Dear {name},

Your Lead (ID: {lead_id}) has been changed status to BOOKED!
Booked quote UID: {quote_uid}

You can view lead here: {url}

Regards,
Sales - Kivork",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/booked/' . $this->id,
                            'lead_id' => $this->id,
                            'quote_uid' => $quote ? $quote->uid : '-',
                            'br' => "\r\n"
                        ]);


                } elseif ($type === 'lead-status-snooze') {

                    $subject = Yii::t('email', "⏱ [Sales] Your Lead-{id} has been changed status to SNOOZE", ['id' => $this->id]);
                    $body = Yii::t('email', "Dear {name},

Your Lead (ID: {lead_id}) has been changed status to SNOOZE!
Snooze for: {datetime}.
Reason: {reason}

You can view lead here: {url}

Regards,
Sales - Kivork",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/booked/' . $this->id,
                            'datetime' => Yii::$app->formatter->asDatetime(strtotime($this->snooze_for)),
                            'reason' => $this->status_description ?: '-',
                            'lead_id' => $this->id,
                            'br' => "\r\n"
                        ]);


                } elseif ($type === 'lead-status-follow-up') {

                    $subject = Yii::t('email', "⚠ [Sales] Your Lead-{id} has been changed status to FOLLOW-UP", ['id' => $this->id]);
                    $body = Yii::t('email', "Dear {name},

Your Lead (ID: {lead_id}) has been changed status to FOLLOW-UP!
Reason: {reason}

You can view lead here: {url}

Regards,
Sales - Kivork",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/booked/' . $this->id,
                            'reason' => $this->status_description ?: '-',
                            'lead_id' => $this->id,
                            'br' => "\r\n"
                        ]);

                }


                try {
                    $isSend = $swiftMailer
                        ->compose()//'sendDeliveryEmailForClient', ['order' => $this])
                        ->setTo($user->email)
                        ->setBcc(Yii::$app->params['email_to']['bcc_sales'])
                        ->setFrom(Yii::$app->params['email_from']['sales'])
                        ->setSubject($subject)
                        ->setTextBody($body)
                        ->send();

                    if (!$isSend) {
                        Yii::warning('Not send to Email:' . $user->email . ' - Sale Id: ' . $this->id, 'Lead:' . $type . ':SendMail');
                    }

                } catch (\Throwable $e) {
                    Yii::error($user->email . ' ' . $e->getMessage(), 'swiftMailer::send');
                }

            } else {
                Yii::warning("Not found employee (" . $employee_id . ") or email: " . ($user ? $user->email : ''), 'Lead:' . $type . ':SendMail');
            }
        } else {
            Yii::warning("type = $type, employee_id = $employee_id, employee2_id = $employee2_id", 'Lead:' . $type . ':SendMail');
        }

        return $isSend;
    }

    public function sendClonedEmail(Lead $lead)
    {
        $isSend = false;

        $host = \Yii::$app->params['url_address'];
        if (isset(Yii::$app->params['email_from']['sales'])) {
            $swiftMailer = Yii::$app->mailer2;
            $user = Employee::findOne($lead->employee_id);

            if (!empty($user)) {
                $agent = $user->username;
                $subject = Yii::t('email', "⚑ [Sales] Cloned Lead-{id} by {agent}", ['id' => $lead->clone_id, 'agent' => $agent]);
                $body = Yii::t('email', "Agent {agent} cloned lead {clone_id} with reason [{reason}], url: {cloned_url}.
New lead {lead_id} you can view here: {url}

Regards,
Sales - Kivork",
                    [
                        'agent' => $agent,
                        'url' => $host . '/lead/processing/' . $lead->id,
                        'cloned_url' => $host . '/lead/processing/' . $lead->clone_id,
                        'reason' => $lead->description,
                        'lead_id' => $lead->id,
                        'clone_id' => $lead->clone_id,
                        'br' => "\r\n"
                    ]);

                $emailTo = Yii::$app->params['email_to']['bcc_sales'];
                try {
                    $isSend = $swiftMailer
                        ->compose()
                        ->setTo($emailTo)
                        ->setFrom(Yii::$app->params['email_from']['sales'])
                        ->setSubject($subject)
                        ->setTextBody($body)
                        ->send();

                    if (!$isSend) {
                        Yii::warning('Not send to Email:' . Yii::$app->params['email_to']['bcc_sales'] . ' - Sale Id: ' . $lead->id, 'Lead:Cloned :SendMail');
                    }

                } catch (\Throwable $e) {
                    Yii::error($user->email . ' ' . $e->getMessage(), 'swiftMailer::send');
                }
            } else {
                Yii::warning("Not found employee (" . $lead->employee_id . "), Lead:Cloned :SendMail");
            }
        }

        return $isSend;
    }


    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            LeadFlow::addStateFlow($this);
        } else {


            if (isset($changedAttributes['status']) && $changedAttributes['status'] !== $this->status) {
                LeadFlow::addStateFlow($this);
            }


            if ($this->status != self::STATUS_TRASH && isset($changedAttributes['employee_id']) && $this->employee_id && $changedAttributes['employee_id'] != $this->employee_id) {
                //echo $changedAttributes['employee_id'].' - '. $this->employee_id;

                if (isset($changedAttributes['status']) && ($changedAttributes['status'] == self::STATUS_TRASH || $changedAttributes['status'] == self::STATUS_FOLLOW_UP)) {

                } else {

                    if (!$this->sendNotification('reassigned-lead', $changedAttributes['employee_id'], $this->employee_id)) {
                        Yii::warning('Not send Email notification to employee_id: ' . $changedAttributes['employee_id'] . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                    }
                }
            }

            if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {


                if ($this->status == self::STATUS_SOLD) {
                    //echo $changedAttributes['status'].' - '. $this->status; exit;
                    if ($this->employee_id && !$this->sendNotification('lead-status-sold', $this->employee_id)) {
                        Yii::warning('Not send Email notification to employee_id: ' . $this->employee_id . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                    }
                } elseif ($this->status == self::STATUS_BOOKED) {

                    if ($this->employee_id && !$this->sendNotification('lead-status-booked', $this->employee_id, null, $this)) {
                        Yii::warning('Not send Email notification to employee_id: ' . $this->employee_id . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                    }
                } elseif ($this->status == self::STATUS_FOLLOW_UP) {

                    $this->l_grade = (int)$this->l_grade + 1;
                    Yii::$app->db->createCommand('UPDATE ' . Lead::tableName() . ' SET l_grade = :grade WHERE id = :id', [
                        ':grade' => $this->l_grade,
                        ':id' => $this->id
                    ])->execute();

                    if ($this->status_description) {
                        $reason = new Reason();
                        $reason->lead_id = $this->id;
                        $reason->employee_id = $this->employee_id;
                        $reason->created = date('Y-m-d H:i:s');
                        $reason->reason = $this->status_description;
                        $reason->save();
                    }

                    /*if (!$this->sendNotification('lead-status-booked', $this->employee_id, null, $this)) {
                        Yii::warning('Not send Email notification to employee_id: ' . $this->employee_id . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                    }*/
                } elseif ($this->status == self::STATUS_SNOOZE) {

                    if ($this->status_description) {
                        $reason = new Reason();
                        $reason->lead_id = $this->id;
                        $reason->employee_id = $this->employee_id;
                        $reason->created = date('Y-m-d H:i:s');
                        $reason->reason = $this->status_description;
                        $reason->save();
                    }


                    if ($this->employee_id && !$this->sendNotification('lead-status-snooze', $this->employee_id, null, $this)) {
                        Yii::warning('Not send Email notification to employee_id: ' . $this->employee_id . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                    }

                }
            }

        }

        if (!$insert) {
            foreach (['updated', 'created'] as $item) {
                if (in_array($item, array_keys($changedAttributes))) {
                    unset($changedAttributes[$item]);
                }
            }
            $flgUnActiveRequest = false;
            $resetCallExpert = false;
            if (isset($changedAttributes['adults']) && $changedAttributes['adults'] != $this->adults) {
                $flgUnActiveRequest = true;
            }
            if (isset($changedAttributes['children']) && $changedAttributes['children'] != $this->children) {
                $flgUnActiveRequest = true;
            }
            if (isset($changedAttributes['infants']) && $changedAttributes['infants'] != $this->infants) {
                $flgUnActiveRequest = true;
            }
            if (isset($changedAttributes['cabin']) && $changedAttributes['cabin'] != $this->cabin) {
                $resetCallExpert = true;
            }
            if (isset($changedAttributes['notes_for_experts']) && $changedAttributes['notes_for_experts'] != $this->notes_for_experts) {
                $resetCallExpert = true;
            }

            if ($resetCallExpert || $flgUnActiveRequest) {
                Yii::$app->db->createCommand('UPDATE ' . Lead::tableName() . ' SET called_expert = :called_expert WHERE id = :id', [
                    ':called_expert' => false,
                    ':id' => $this->id
                ])->execute();
            }

            if ($flgUnActiveRequest) {
                foreach ($this->getAltQuotes() as $quote) {
                    if ($quote->status != $quote::STATUS_APPLIED) {
                        $quote->status = $quote::STATUS_DECLINED;
                        $quote->save(false);
                    }
                }
            }
        }

        //Add logs after changed model attributes
        $leadLog = new LeadLog(new LeadLogMessage());
        $leadLog->logMessage->oldParams = $changedAttributes;
        $leadLog->logMessage->newParams = array_intersect_key($this->attributes, $changedAttributes);
        $leadLog->logMessage->title = ($insert)
            ? 'Create' : 'Update';
        $leadLog->logMessage->model = $this->formName();
        $leadLog->addLog([
            'lead_id' => $this->id,
        ]);
    }

    /**
     * @return array|Quote[]
     */
    public function getAltQuotes()
    {
        return Quote::find()->where(['lead_id' => $this->id])
            ->orderBy('id DESC')->all();
    }

    public function getClientTime($id = null)
    {
        if (!empty($id)) {
            $model = self::findOne(['id' => $id]);
        } else {
            $model = self::findOne(['id' => $this->id]);
        }
        $offset = '';
        $spanId = sprintf('sale-client-time-%d', $model->id);
        if (!empty($model->offset_gmt)) {
            $offset = $model->offset_gmt;
        } elseif (count($model->leadFlightSegments)) {
            $firstSegment = $model->leadFlightSegments[0];
            $airport = Airport::findIdentity($firstSegment->origin);
            if ($airport !== null && !empty($airport->dst)) {
                $offset = $airport->dst;
            }
        }

        if (!empty($offset)) {
            $content = '<span class="sale-client-time" id="' . $spanId . '" data-offset="' . $offset . '"></span>';
            if (!empty($model->request_ip_detail)) {
                $ipData = @json_decode($model->request_ip_detail, true);
                if (isset($ipData['country_code'])) {
                    $content .= '&nbsp;' . Html::tag('i', '', [
                            'class' => 'flag flag__' . strtolower($ipData['country_code']),
                            'style' => 'vertical-align: middle;'
                        ]);
                }
            }
            return $content;
        }

        return '';
    }


    public function getClientTime2()
    {
        $clientTime = '-';
        $offset_gmt = $this->offset_gmt;
        $offset = false;


        if ($offset_gmt) {
            $offset = str_replace('.', ':', $offset_gmt);

            if (isset($offset[0])) {
                if (strpos($offset, '+') === 0) {
                    $offset = str_replace('+', '-', $offset);
                } else {
                    $offset = str_replace('-', '+', $offset);
                }
            }


        } else {

            if ($this->leadFlightSegments) {
                $firstSegment = $this->leadFlightSegments[0];
                $airport = Airport::findIdentity($firstSegment->origin);
                if ($airport && $airport->dst) {
                    $offset = $airport->dst;
                    $offset_gmt = $airport->dst;
                }
            }

        }

        if ($offset) {
            $clientTime = date("H:i", strtotime("now $offset GMT"));
            $clientTime = '<i class="fa fa-clock-o"></i> <b>' . Html::encode($clientTime) . '</b>'; //<br/>(GMT: ' .$offset_gmt . ')';
        }

        return $clientTime;
    }


    /**
     * @return array|Note[]
     */
    public function getNotes()
    {
        return Note::find()->where(['lead_id' => $this->id])
            ->orderBy('id DESC')->all();
    }

    /**
     * @return array|Note[]
     */
    public function getLogs()
    {
        return LeadLog::find()->where(['lead_id' => $this->id])
            ->orderBy('id DESC')->all();
    }

    public function getSentCount()
    {
        $data = Quote::find()
            ->where(['lead_id' => $this->id, 'status' => [
                Quote::STATUS_SEND,
                Quote::STATUS_OPENED,
                Quote::STATUS_APPLIED]
            ])->all();
        return count($data);
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function lastLog()
    {
        return LeadLog::find()->where([
            'lead_id' => $this->id,
        ])->orderBy('id DESC')->one();
    }

    /**
     * @return Reason
     */
    public function lastReason()
    {
        return Reason::find()
            ->where(['lead_id' => $this->id])
            ->orderBy('id desc')->one();
    }

    /**
     * @return Quote|null
     */
    public function getAppliedAlternativeQuotes()
    {
        return Quote::findOne([
            'lead_id' => $this->id,
            'status' => Quote::STATUS_APPLIED
        ]);
    }

    public function getQuotesCount(): int
    {
        return $this->hasMany(Quote::class, ['lead_id' => 'id'])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlightSegments(): ActiveQuery
    {
        return $this->hasMany(LeadFlightSegment::class, ['lead_id' => 'id']);
    }

    /**
     * @return int
     */
    public function getLeadFlightSegmentsCount(): int
    {
        return $this->hasMany(LeadFlightSegment::class, ['lead_id' => 'id'])->count();
    }


    public function getFirstFlightSegment()
    {
        return LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['departure' => 'ASC'])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadPreferences(): ActiveQuery
    {
        return $this->hasOne(LeadPreferences::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource(): ActiveQuery
    {
        return $this->hasOne(Source::class, ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }


    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                //$this->created = date('Y-m-d H:i:s');
                if (!empty($this->project_id) && empty($this->source_id)) {
                    $project = Project::findOne(['id' => $this->project_id]);
                    if ($project !== null) {
                        $this->source_id = $project->sources[0]->id;
                    }
                }

                $leadExistByUID = Lead::findOne([
                    'uid' => $this->uid,
                    'source_id' => $this->source_id
                ]);
                if ($leadExistByUID !== null) {
                    $this->uid = uniqid();
                }
            } else {
                //$this->updated = date('Y-m-d H:i:s');
            }

            $this->adults = (int)$this->adults;
            $this->children = (int)$this->children;
            $this->infants = (int)$this->infants;
            $this->bo_flight_id = sprintf('%d', $this->bo_flight_id);

            return true;
        }
        return false;
    }


    /*public function beforeValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if ($this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s');
            if (!empty($this->project_id) && empty($this->source_id)) {
                $project = Project::findOne(['id' => $this->project_id]);
                if ($project !== null) {
                    $this->source_id = $project->sources[0]->id;
                }
            }
        }

        $this->adults = intval($this->adults);
        $this->children = intval($this->children);
        $this->infants = intval($this->infants);

        return parent::beforeValidate();
    }*/

    public function afterValidate()
    {
        if ($this->isNewRecord && !empty($this->source_id)) {
            $source = Source::findOne(['id' => $this->source_id]);
            if ($source !== null) {
                $this->project_id = $source->project_id;
            }
        }

        if (is_array($this->additional_information)) {
            $this->additional_information = json_encode($this->additional_information);
        } else {
            $separateInfo = [];
            foreach ($this->additionalInformationForm as $additionalInformation) {
                $separateInfo[] = $additionalInformation->attributes;
            }
            $this->additional_information = json_encode($separateInfo);
        }

        parent::afterValidate();
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!empty($this->additional_information)) {
            $this->additionalInformationForm = self::getLeadAdditionalInfo($this->additional_information);
        }
        $this->totalTips = ($this->tips)?$this->tips/2:0;
    }

    /**
     * @param $additionalInfoStr
     * @return LeadAdditionalInformation[]
     */
    public static function getLeadAdditionalInfo($additionalInfoStr)
    {
        $additionalInformationFormArr = [];
        $separateInfoArr = json_decode($additionalInfoStr);
        if (is_array($separateInfoArr)) {
            $separateInfoArr = json_decode($additionalInfoStr, true);
            foreach ($separateInfoArr as $key => $separateInfo) {
                $additionalInfo = new LeadAdditionalInformation();
                $additionalInfo->setAttributes($separateInfo);
                $additionalInformationFormArr[] = $additionalInfo;
            }
        } else {
            $additionalInfo = new LeadAdditionalInformation();
            $additionalInfo->setAttributes(json_decode($additionalInfoStr, true));
            $additionalInformationFormArr[] = $additionalInfo;
        }

        return $additionalInformationFormArr;
    }

    public function getPaxTypes()
    {
        $types = [];
        for ($i = 0; $i < $this->adults; $i++) {
            $types[] = QuotePrice::PASSENGER_ADULT;
        }
        for ($i = 0; $i < $this->children; $i++) {
            $types[] = QuotePrice::PASSENGER_CHILD;
        }
        for ($i = 0; $i < $this->infants; $i++) {
            $types[] = QuotePrice::PASSENGER_INFANT;
        }

        return $types;
    }

    public function sendSoldEmail($data)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];

        $key = sprintf('%s_lead_UID_%s', uniqid(), $this->uid);
        $fileName = sprintf('_%s_%s.php', str_replace(' ', '_', strtolower($this->project->name)), $key);
        $path = sprintf('%s/frontend/views/tmpEmail/quote/%s', dirname(Yii::getAlias('@app')), $fileName);

        $template = ProjectEmailTemplate::findOne([
            'type' => ProjectEmailTemplate::TYPE_EMAIL_TICKET,
            'project_id' => $this->project_id
        ]);

        if ($template === null) {
            $result['errors'][] = sprintf('Email Template [%s] for project [%s] not fond.',
                ProjectEmailTemplate::getTypes(ProjectEmailTemplate::TYPE_EMAIL_TICKET),
                $this->project->name
            );
            return $result;
        }

        $view = $template->template;
        $fp = fopen($path, "w");
        chmod($path, 0777);
        fwrite($fp, $view);
        fclose($fp);

        $body = \Yii::$app->getView()->renderFile($path, [
            'model' => $this,
            'uid' => $this->getAppliedAlternativeQuotes()->uid,
            'flightRequest' => $data,
        ]);

        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);
        $credential = [
            'email' => $userProjectParams->upp_email,
        ];

        if (!empty($template->layout_path)) {
            $body = \Yii::$app->getView()->renderFile($template->layout_path, [
                'project' => $this->project,
                'agentName' => ucfirst($this->employee->username),
                'employee' => $this->employee,
                'userProjectParams' => $userProjectParams,
                'body' => $body,
                'templateType' => $template->type,
            ]);
        }

        $subject = ProjectEmailTemplate::getMessageBody($template->subject, [
            'pnr' => $data['pnr'],
        ]);

        $errors = [];
        $bcc = [
            trim($userProjectParams->upp_email),
            'damian.t@wowfare.com',
            'andrew.t@wowfare.com'
        ];
        $isSend = EmailService::sendByAWS($data['emails'], $this->project, $credential, $subject, $body, $errors, $bcc);
        $message = ($isSend)
            ? sprintf('Sending email - \'Tickets\' succeeded! <br/>Emails: %s',
                implode(', ', $data['emails'])
            )
            : sprintf('Sending email - \'Tickets\' failed! <br/>Emails: %s',
                implode(', ', $data['emails'])
            );

        //Add logs after changed model attributes
        $leadLog = new LeadLog((new LeadLogMessage()));
        $leadLog->logMessage->message = empty($errors)
            ? $message
            : sprintf('%s <br/>Errors: %s', $message, print_r($errors, true));
        $leadLog->logMessage->title = 'Send Tickets by Email';
        $leadLog->logMessage->model = $this->formName();
        $leadLog->addLog([
            'lead_id' => $this->id,
        ]);

        $result['status'] = $isSend;
        $result['errors'] = $errors;

        unlink($path);

        return $result;
    }

    public function previewEmail($quotes, $email)
    {
        $result = [
            'status' => false,
            'errors' => [],
            'email' => [],
        ];
        $models = [];
        $i = 1;
        foreach ($quotes as $quote) {
            $model = Quote::findOne([
                'uid' => $quote
            ]);
            if ($model !== null) {
                $models[$i] = $model;
                $i++;
            }
        }

        if (empty($models)) {
            $result['errors'][] = sprintf('Quotes not found. UID: [%s]', implode(', ', $quotes));
            return $result;
        }

        $key = sprintf('%s_%s', uniqid(), $email);
        $fileName = sprintf('_%s_%s.php', str_replace(' ', '_', strtolower($this->project->name)), $key);
        $path = sprintf('%s/tmpEmail/quote/%s', Yii::$app->getViewPath(), $fileName);

        $template = ProjectEmailTemplate::findOne([
            'type' => ProjectEmailTemplate::TYPE_EMAIL_OFFER,
            'project_id' => $this->project_id
        ]);

        if ($template === null) {
            $result['errors'][] = sprintf('Email Template [%s] for project [%s] not fond.',
                ProjectEmailTemplate::getTypes(ProjectEmailTemplate::TYPE_EMAIL_OFFER),
                $this->project->name
            );
            return $result;
        }

        $view = $template->template;
        $fp = fopen($path, "w");
        chmod($path, 0777);
        fwrite($fp, $view);
        fclose($fp);

        $view = sprintf('/tmpEmail/quote/%s', $fileName);

        $airport = Airport::findIdentity($this->leadFlightSegments[0]->origin);
        $origin = ($airport !== null)
            ? $airport->city :
            $this->leadFlightSegments[0]->origin;

        $airport = Airport::findIdentity($this->leadFlightSegments[0]->destination);
        $destination = ($airport !== null)
            ? $airport->city
            : $this->leadFlightSegments[0]->destination;

        $tripType = Lead::getFlightType($this->trip_type);

        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);


        $body = Yii::$app->getView()->render($view, [
            'origin' => $origin,
            'destination' => $destination,
            'quotes' => $models,
            'leadCabin' => self::getCabin($this->cabin),
            'nrPax' => ($this->adults + $this->children + $this->infants),
            'project' => $this->project,
            'agentName' => ucfirst($this->employee->username),
            'employee' => $this->employee,
            'tripType' => $tripType,
            'userProjectParams' => $userProjectParams,
        ]);

        if (!empty($template->layout_path)) {
            $result['email']['body'] = \Yii::$app->getView()->renderFile($template->layout_path, [
                'project' => $this->project,
                'agentName' => ucfirst($this->employee->username),
                'employee' => $this->employee,
                'userProjectParams' => $userProjectParams,
                'body' => $body,
                'templateType' => $template->type,
            ]);
        }

        $result['email']['subject'] = ProjectEmailTemplate::getMessageBody($template->subject, [
            'origin' => $origin,
            'destination' => $destination
        ]);
        unlink($path);


        return $result;
    }


    public function sendEmail($quotes, $email)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        $models = [];
        $i = 1;
        foreach ($quotes as $quote) {
            $model = Quote::findOne([
                'uid' => $quote
            ]);
            if ($model !== null) {
                $models[$i] = $model;
                $i++;
            }
        }

        if (empty($models)) {
            $result['errors'][] = sprintf('Quotes not found. UID: [%s]', implode(', ', $quotes));
            return $result;
        }

        $key = sprintf('%s_%s', uniqid(), $email);
        $fileName = sprintf('_%s_%s.php', str_replace(' ', '_', strtolower($this->project->name)), $key);
        $path = sprintf('%s/tmpEmail/quote/%s', Yii::$app->getViewPath(), $fileName);

        $template = ProjectEmailTemplate::findOne([
            'type' => ProjectEmailTemplate::TYPE_EMAIL_OFFER,
            'project_id' => $this->project_id
        ]);

        if ($template === null) {
            $result['errors'][] = sprintf('Email Template [%s] for project [%s] not fond.',
                ProjectEmailTemplate::getTypes(ProjectEmailTemplate::TYPE_EMAIL_OFFER),
                $this->project->name
            );
            return $result;
        }

        $view = $template->template;
        $fp = fopen($path, "w");
        chmod($path, 0777);
        fwrite($fp, $view);
        fclose($fp);

        $view = sprintf('/tmpEmail/quote/%s', $fileName);

        $airport = Airport::findIdentity($this->leadFlightSegments[0]->origin);
        $origin = ($airport !== null)
            ? $airport->city :
            $this->leadFlightSegments[0]->origin;

        $airport = Airport::findIdentity($this->leadFlightSegments[0]->destination);
        $destination = ($airport !== null)
            ? $airport->city
            : $this->leadFlightSegments[0]->destination;

        $tripType = Lead::getFlightType($this->trip_type);

        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);

        $body = Yii::$app->getView()->render($view, [
            'origin' => $origin,
            'destination' => $destination,
            'quotes' => $models,
            'leadCabin' => self::getCabin($this->cabin),
            'nrPax' => ($this->adults + $this->children + $this->infants),
            'project' => $this->project,
            'agentName' => ucfirst($this->employee->username),
            'employee' => $this->employee,
            'tripType' => $tripType,
            'userProjectParams' => $userProjectParams
        ]);

        if (!empty($template->layout_path)) {
            $body = \Yii::$app->getView()->renderFile($template->layout_path, [
                'project' => $this->project,
                'agentName' => ucfirst($this->employee->username),
                'employee' => $this->employee,
                'userProjectParams' => $userProjectParams,
                'body' => $body,
                'templateType' => $template->type,
            ]);
        }

        $subject = ProjectEmailTemplate::getMessageBody($template->subject, [
            'origin' => $origin,
            'destination' => $destination
        ]);

        $credential = [
            'email' => trim($userProjectParams->upp_email),
        ];

        $errors = [];
        $bcc = [
            trim($userProjectParams->upp_email),
            'damian.t@wowfare.com',
            'andrew.t@wowfare.com'
        ];
        $isSend = EmailService::sendByAWS($email, $this->project, $credential, $subject, $body, $errors, $bcc);
        $message = ($isSend)
            ? sprintf('Sending email - \'Offer\' succeeded! <br/>Emails: %s <br/>Quotes: %s',
                implode(', ', [$email]),
                implode(', ', $quotes)
            )
            : sprintf('Sending email - \'Offer\' failed! <br/>Emails: %s <br/>Quotes: %s',
                implode(', ', [$email]),
                implode(', ', $quotes)
            );

        //Add logs after changed model attributes
        $leadLog = new LeadLog((new LeadLogMessage()));
        $leadLog->logMessage->message = empty($errors)
            ? $message
            : sprintf('%s <br/>Errors: %s', $message, print_r($errors, true));
        $leadLog->logMessage->title = 'Send Quotes by Email';
        $leadLog->logMessage->model = $this->formName();
        $leadLog->addLog([
            'lead_id' => $this->id,
        ]);

        $result['status'] = $isSend;
        $result['errors'] = $errors;

        unlink($path);

        return $result;
    }


    public function getEmailData($quotesUids)
    {

        $airport = Airport::findIdentity($this->leadFlightSegments[0]->origin);
        $origin = ($airport !== null) ? $airport->city : $this->leadFlightSegments[0]->origin;

        $airport = Airport::findIdentity($this->leadFlightSegments[0]->destination);
        $destination = ($airport !== null) ? $airport->city : $this->leadFlightSegments[0]->destination;

        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);

        $quotesData = [];
        foreach ($quotesUids as $uid){
            $quote = Quote::findOne(['uid' => $uid]);
            if($quote !== null){
                $segmentsData = [];
                $trips = $quote->quoteTrips;
                foreach ($trips as $trip){
                    $segments = $trip->quoteSegments;
                    if( $segments ) {
                        $segmentsCnt = count($segments);
                        $stopCnt = $segmentsCnt - 1;
                        $firstSegment = $segments[0];
                        $lastSegment = end($segments);
                        $cabins = [];
                        $marketingAirlines = [];
                        $airlineNames = [];
                        foreach ($segments as $segment){
                            if(!in_array(SearchService::getCabin($segment->qs_cabin), $cabins)){
                                $cabins[] = SearchService::getCabin($segment->qs_cabin);
                            }
                            if(isset($segment->qs_stop) && $segment->qs_stop > 0){
                                $stopCnt += $segment->qs_stop;
                            }
                            if(!in_array($segment->qs_marketing_airline, $marketingAirlines)){
                                $marketingAirlines[] = $segment->qs_marketing_airline;
                                $airline = Airline::findIdentity($segment->qs_marketing_airline);
                                if($airline){
                                    $airlineNames[] =  $airline->name;
                                }else{
                                    $airlineNames[] = $segment->qs_marketing_airline;
                                }

                            }
                        }
                    } else {
                        continue;
                    }

                    $segmentsData[] = [
                        'airlineIata' => (count($marketingAirlines) == 1)?$marketingAirlines[0]:'multiple_airlines',
                        'airlineLogoUrl' => (count($marketingAirlines) == 1)?'//www.gstatic.com/flights/airline_logos/70px/'.$marketingAirlines[0].'.png':'/img/multiple_airlines.png',
                        'departDate' => Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'MMM d'),
                        'departTime' => Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'h:mm a'),
                        'originIata' => $firstSegment->qs_departure_airport_code,
                        'originCity' => ($firstSegment->departureAirport)?$firstSegment->departureAirport->city:$firstSegment->qs_departure_airport_code,
                        'flightDuration' => SearchService::durationInMinutes($trip->qt_duration),
                        'stopsQuantity' => \Yii::t('search', '{n, plural, =0{Nonstop} one{# stop} other{# stops}}', ['n' => $stopCnt]),
                        'destinationIata' => $lastSegment->qs_arrival_airport_code,
                        'destinationCity' => ($lastSegment->arrivalAirport)?$lastSegment->arrivalAirport->city:$lastSegment->qs_arrival_airport_code,
                        'arriveTime' => Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time),'h:mm a'),
                        'arriveDate' => Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time),'MMM d')
                    ];
                }
                $quotesData[] = [
                    'currency' => 'USD',
                    'price' => $quote->getPricePerPax(),
                    'url' => $this->project->link.'/checkout/'.$quote->uid,
                    'segments' => $segmentsData
                ];
            }
        }

        $emailData = [
            'project_url' => $this->project->link,
            'agent' => [
                'name' => ucfirst($this->employee->username),
                'email' => trim($userProjectParams->upp_email)
            ],
            'request' => [
                'originCity' => $origin,
                'destinationCity' => $destination,
                'cabinType' => self::getCabin($this->cabin),
                'tripType' => strtolower($this->trip_type),
                'pax' => ($this->adults + $this->children + $this->infants)
            ],
            'quotes' => $quotesData,
            'contacts' => [
                'phone' => $this->project->contactInfo->phone,
                'email' => $this->project->contactInfo->email,
            ],
        ];

        return $emailData;
    }

    public static function getFlightType($flightType = null)
    {
        $mapping = self::TRIP_TYPE_LIST;

        if ($flightType === null) {
            return $mapping;
        }

        return isset($mapping[$flightType]) ? $mapping[$flightType] : $flightType;
    }

    public function getLeadInformationForExpert()
    {
        $information = [
            'trip_type' => $this->trip_type,
            'cabin' => $this->cabin,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            'notes_for_experts' => $this->notes_for_experts,
            'pref_airline' => !empty($this->leadPreferences)
                ? $this->leadPreferences->pref_airline : '',
            'number_stops' => !empty($this->leadPreferences)
                ? $this->leadPreferences->number_stops : '',
            'clients_budget' => !empty($this->leadPreferences)
                ? $this->leadPreferences->clients_budget : '',
            'market_price' => !empty($this->leadPreferences)
                ? $this->leadPreferences->market_price : '',
            'itinerary' => [],
            'agent_name' => ($this->employee !== null)
                ? $this->employee->username
                : 'N/A'
        ];

        $itinerary = [];
        foreach ($this->leadFlightSegments as $leadFlightSegment) {
            $itinerary[] = [
                'route' => sprintf('%s - %s', $leadFlightSegment->origin, $leadFlightSegment->destination),
                'date' => $leadFlightSegment->departure,
                'flex' => empty($leadFlightSegment->flexibility)
                    ? '' : sprintf('%s %d',
                        $leadFlightSegment->flexibility_type,
                        $leadFlightSegment->flexibility
                    )
            ];
        }
        $information['itinerary'] = $itinerary;

        $quoteArr = [];
        foreach ($this->getQuotes() as $quote) {
            $quoteArr[] = $quote->getQuoteInformationForExpert();
        }

        return [
            'call_expert' => false,
            'LeadRequest' => [
                'uid' => $this->uid,
                'market_info_id' => $this->source_id,
                'information' => $information
            ],
            'LeadQuotes' => $quoteArr
        ];
    }

    /**
     * @return Quote[]
     */
    public function getQuotes()
    {
        return Quote::findAll(['lead_id' => $this->id]);
    }

    /**
     * @param null $role
     * @return array
     */
    public static function getStatusList($role = null)
    {

        switch ($role) {
            case 'admin' :
                $list = self::STATUS_LIST;
                break;
            case 'supervision' :
                $list = self::STATUS_MULTIPLE_UPDATE_LIST;
                break;
            default :
                $list = self::STATUS_LIST;
        }

        return $list;
    }

    public static function getProcessingStatuses()
    {
        return [
            self::STATUS_SNOOZE => self::STATUS_LIST[self::STATUS_SNOOZE],
            self::STATUS_PROCESSING => self::STATUS_LIST[self::STATUS_PROCESSING],
            self::STATUS_ON_HOLD => self::STATUS_LIST[self::STATUS_ON_HOLD],
        ];
    }

    /**
     * @return string
     */
    public function getTaskInfo(): string
    {

        $taskListAll = \common\models\LeadTask::find()->select(['COUNT(*) AS field_cnt', 'lt_task_id'])->where(['lt_lead_id' => $this->id])->groupBy(['lt_task_id'])->all();
        $taskListChecked = \common\models\LeadTask::find()->select(['COUNT(*) AS field_cnt', 'lt_task_id'])->where(['lt_lead_id' => $this->id])->andWhere(['IS NOT', 'lt_completed_dt', null])->groupBy(['lt_task_id'])->all();

        $completed = [];
        if ($taskListChecked) {
            foreach ($taskListChecked as $taskItem) {
                $completed[$taskItem->lt_task_id] = $taskItem->field_cnt;
            }
        }

        $item = [];

        if ($taskListAll) {
            foreach ($taskListAll as $task) {
                $item[] = $task->ltTask->t_name . ' - (' . ($completed[$task->lt_task_id] ?? 0) . '/' . $task->field_cnt . ')';
            }
        }

        return implode('<br> ', $item);
    }

    /**
     * @param int $lead_id
     * @return string
     */
    public static function getTaskInfo2(int $lead_id): string
    {
        $lead = Lead::findOne($lead_id);
        if ($lead) {
            $out = $lead->getTaskInfo();
        } else {
            $out = '';
        }
        return $out;
    }

    /**
     * @param int|null $category_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getEndTaskLeads(int $category_id = null): array
    {

        $query = new Query();
        $query->select(['lt.lt_lead_id', 'lt.lt_user_id', 'l.status', 'l.l_answered', 't.t_category_id']);

        $query->addSelect('(SELECT COUNT(*) FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id AND lt_completed_dt IS NOT NULL) AS checked_cnt');

        $query->addSelect('(SELECT COUNT(*) FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id) AS all_cnt');

        $query->addSelect('(SELECT lt_date FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id
ORDER BY lt_date DESC LIMIT 1) AS last_task_date');

        $query->from('lead_task AS lt');
        $query->innerJoin('task AS t', 't.t_id = lt.lt_task_id');
        $query->innerJoin('leads AS l', 'l.id = lt.lt_lead_id');
        $query->where(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD]]);
        $query->andWhere(['=', 'l.employee_id', new Expression('`lt`.`lt_user_id`')]);


        $query->andWhere(['>', '(SELECT COUNT(*) FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id)', '0']);

        $query->andWhere([
            '=',
            new Expression('(SELECT COUNT(*) FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id AND lt_completed_dt IS NOT NULL)'),
            new Expression('(SELECT COUNT(*) FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id)')
        ]);

        $query->andWhere(['<', new Expression('(SELECT lt_date FROM lead_task
INNER JOIN task ON (task.t_id = lead_task.lt_task_id)
WHERE lt_lead_id = lt.lt_lead_id AND lt_user_id = lt.lt_user_id AND t_category_id = t.t_category_id
ORDER BY lt_date DESC LIMIT 1)'), date('Y-m-d')]);

        $query->andFilterWhere(['t.t_category_id' => $category_id]);
        $query->groupBy(['lt.lt_lead_id', 'lt.lt_user_id', 't.t_category_id']);

        $command = $query->createCommand();

        //echo $command->getRawSql(); exit;

        //VarDumper::dump($command->queryAll()); exit;

        return $command->queryAll();
    }

    public function getBookedQuote()
    {
        return Quote::findOne(['lead_id' => $this->id, 'status' => Quote::STATUS_APPLIED]);
    }

    public function getFlightDetails()
    {
        $flightSegments = LeadFlightSegment::findAll(['lead_id' => $this->id]);
        $segmentsStr = [];
        foreach ($flightSegments as $entry) {
            $segmentsStr[] = $entry['departure'] . ' ' . $entry['origin'] . '-' . $entry['destination'];
        }

        return implode('<br/>', $segmentsStr);
    }

    public function getDeparture()
    {
        $flightSegment = LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['departure' => SORT_ASC])->one();

        return ($flightSegment) ? $flightSegment['departure'] : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfitSplits()
    {
        return $this->hasMany(ProfitSplit::className(), ['ps_lead_id' => 'id']);
    }

    public function getAllProfitSplits()
    {
        return ProfitSplit::find()->where(['ps_lead_id' => $this->id])->all();
    }

    public function getSumPercentProfitSplit()
    {
        $query = new Query();
        $query->from(ProfitSplit::tableName() . ' ps')
            ->where(['ps.ps_lead_id' => $this->id])
            ->select(['SUM(ps.ps_percent) as percent']);

        return $query->queryScalar();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipsSplits()
    {
        return $this->hasMany(TipsSplit::className(), ['ts_lead_id' => 'id']);
    }

    public function getAllTipsSplits()
    {
        return TipsSplit::find()->where(['ts_lead_id' => $this->id])->all();
    }

    public function getSumPercentTipsSplit()
    {
        $query = new Query();
        $query->from(TipsSplit::tableName() . ' ts')
        ->where(['ts.ts_lead_id' => $this->id])
        ->select(['SUM(ts.ts_percent) as percent']);

        return $query->queryScalar();
    }

    public function getQuoteSendInfo()
    {
        $query = new Query();
        $query->select(['SUM(CASE WHEN status IN (2, 4, 5) THEN 1 ELSE 0 END) AS send_q',
            'SUM(CASE WHEN status NOT IN (2, 4, 5) THEN 1 ELSE 0 END) AS not_send_q'])
            ->from(Quote::tableName() . ' q')
            ->where(['lead_id' => $this->id]);
        //->groupBy('lead_id');

        return $query->createCommand()->queryOne();
    }

    public function getLastActivityByNote()
    {
        $lastNote = Note::find()->where(['lead_id' => $this->id])->orderBy(['created' => SORT_DESC])->one();

        if (!empty($lastNote)) {
            return $lastNote['created'];
        }

        return $this->updated;
    }

    public function getLastReason()
    {
        $lastReason = Reason::find()->where(['lead_id' => $this->id])->orderBy(['created' => SORT_DESC])->one();

        if (!empty($lastReason)) {
            return $lastReason['reason'];
        }

        return '-';
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function getQuotesProvider($params)
    {
        $query = Quote::find()->where(['lead_id' => $this->id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }


    public function generateLeadKey()
    {
        $leadFlights = $this->leadFlightSegments;
        $key = $this->cabin;
        foreach ($leadFlights as $flEntry){
            $key .= $flEntry->origin.$flEntry->destination.strtotime($flEntry->departure).$flEntry->flexibility_type.$flEntry->flexibility;
        }
        $key .= '_'.$this->adults.'_'.$this->children.'_'.$this->infants;
        return $key;
    }
}
