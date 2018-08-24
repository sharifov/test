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
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
 *
 * @property LeadFlightSegment[] $leadFlightSegments
 * @property LeadPreferences $leadPreferences
 * @property Client $client
 * @property Employee $employee
 * @property Source $source
 * @property Project $project
 * @property int $quotesCount
 * @property int $leadFlightSegmentsCount
 * @property LeadAdditionalInformation $additionalInformationForm
 */
class Lead extends ActiveRecord
{
    public $additionalInformationForm;

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
        self::STATUS_FOLLOW_UP => 'Follow Up',
        self::STATUS_ON_HOLD => 'Hold On',
        self::STATUS_SOLD => 'Sold',
        self::STATUS_TRASH => 'Trash',
        self::STATUS_BOOKED => 'Booked',
        self::STATUS_SNOOZE => 'Snooze',
    ];

    public CONST STATUS_CLASS_LIST = [
        self::STATUS_PENDING        => 'll-pending',
        self::STATUS_PROCESSING     => 'll-processing',
        self::STATUS_FOLLOW_UP      => 'll-follow_up',
        self::STATUS_ON_HOLD        => 'll-on_hold',
        self::STATUS_SOLD           => 'll-sold',
        self::STATUS_TRASH          => 'll-trash',
        self::STATUS_BOOKED         => 'll-booked',
        self::STATUS_SNOOZE         => 'll-snooze',
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

    public function init()
    {
        parent::init();

        $this->additionalInformationForm = new LeadAdditionalInformation();
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

            if (Yii::$app->user->identity->role == 'agent' && in_array($key, ['trash'])) {
                $badges[$key] = 0;
                continue;
            }

            if (Yii::$app->user->identity->role == 'agent' && in_array($key, ['sold'])) {
                $query->andWhere([
                    'employee_id' => Yii::$app->user->identity->getId()
                ]);
            }

            if (in_array($key, ['processing'])) {
                $query->andWhere([
                    'employee_id' => Yii::$app->user->identity->getId()
                ]);
            }

            $badges[$key] = $query->count();
        }

        return $badges;
    }

    public static function getLeadQueueType()
    {
        return [
            'inbox', 'follow-up', 'processing',
            'processing-all', 'booked', 'sold', 'trash'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leads';
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


        $query = self::find()
            ->where(['IN', self::getTableSchema()->fullName . '.status', $status])
            ->andWhere(['IN', self::getTableSchema()->fullName . '.project_id', $projectIds]);

        if (Yii::$app->user->identity->role == 'agent' && in_array($queue, ['sold'])) {
            $query->andWhere([
                'employee_id' => Yii::$app->user->identity->getId()
            ]);
        }

        if ($searchModel !== null && in_array($queue, ['processing-all', 'trash'])) {
            $query->andFilterWhere([self::getTableSchema()->fullName . '.employee_id' => $searchModel->employee_id]);
        }

        if ($divGridBy !== null) {
            switch ($divGridBy) {
                case self::DIV_GRID_WITH_OUT_EMAIL:
                    $query->join('LEFT JOIN', ClientEmail::tableName(), ClientEmail::tableName() . '.client_id = ' . Lead::tableName() . '.client_id');
                    $query->andWhere(ClientEmail::tableName() . '.id IS NULL');
                    break;
                case self::DIV_GRID_WITH_EMAIL:
                    $subQuery = new Query();
                    $subQuery->select(['lead_id'])->from(Quote::tableName())->where(['IN', 'status', [
                        Quote::STATUS_SEND,
                        Quote::STATUS_OPENED,
                        Quote::STATUS_APPLIED
                    ]]);
                    $query->join('INNER JOIN', ClientEmail::tableName(), ClientEmail::tableName() . '.client_id = ' . Lead::tableName() . '.client_id');
                    $query->andWhere(['NOT IN', self::getTableSchema()->fullName . '.id', ArrayHelper::map($subQuery->all(), 'lead_id', 'lead_id')]);
                    break;
                case self::DIV_GRID_SEND_QUOTES:
                    $subQuery = new Query();
                    $subQuery->select(['lead_id'])->from(Quote::tableName())->where(['IN', 'status', [
                        Quote::STATUS_SEND,
                        Quote::STATUS_OPENED,
                        Quote::STATUS_APPLIED
                    ]]);
                    $query->andWhere(['IN', self::getTableSchema()->fullName . '.id', ArrayHelper::map($subQuery->all(), 'lead_id', 'lead_id')]);
                    break;
            }
        }

        if (in_array($queue, ['follow-up'])) {
            $showAll = Yii::$app->request->cookies->getValue(self::getCookiesKey(), true);
            if (!$showAll) {
                $query->andWhere(['NOT IN', self::getTableSchema()->fullName . '.id', self::unprocessedByAgentInFollowUp()]);
            }
        }

        if (in_array($queue, ['processing'])) {
            $query->andWhere([
                'employee_id' => Yii::$app->user->identity->getId()
            ]);
        }

        $query->distinct = true;
        /*var_dump($query->createCommand()->rawSql);
        echo '<br><br><br>';*/
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);

        if ($queue != 'trash') {
            $dataProvider->sort->defaultOrder = ['pending' => SORT_DESC];
        } else {
            $dataProvider->sort->defaultOrder = ['pending_in_trash' => SORT_DESC];
            $dataProvider->sort->attributes['pending_in_trash'] = [
                'asc' => [self::getTableSchema()->fullName . '.updated' => SORT_ASC],
                'desc' => [self::getTableSchema()->fullName . '.updated' => SORT_DESC],
            ];
        }
        $dataProvider->sort->attributes['last_activity'] = [
            'asc' => ['notes_group.created' => SORT_DESC],
            'desc' => ['notes_group.created' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['pending'] = [
            'asc' => [Lead::tableName() . '.created' => SORT_ASC],
            'desc' => [Lead::tableName() . '.created' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['rating'] = [
            'asc' => [Lead::tableName() . '.rating' => SORT_ASC],
            'desc' => [Lead::tableName() . '.rating' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sub_source_id'] = [
            'asc' => [Lead::tableName() . '.sub_source_id' => SORT_DESC],
            'desc' => [Lead::tableName() . '.sub_source_id' => SORT_ASC],
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['trip_type', 'cabin'], 'required'],
            [['adults', 'children', 'infants', 'source_id'], 'required'], //'except' => self::SCENARIO_API],

            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'rating'], 'integer'],
            [['adults', 'children', 'infants'], 'integer', 'max' => 9],
            [['adults'], 'integer', 'min' => 1],
            [['notes_for_experts'], 'string'],
            [['created', 'updated', 'offset_gmt', 'request_ip', 'request_ip_detail', 'snooze_for',
                'called_expert', 'discount_id', 'bo_flight_id', 'additional_information'], 'safe'],
            [['uid'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],
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

    public function permissionsView()
    {
        if (Yii::$app->user->identity->role != 'admin') {
            $access = ProjectEmployeeAccess::findOne([
                'employee_id' => Yii::$app->user->identity->getId(),
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

    public function getSnoozeCountdown()
    {
        if (!empty($this->snooze_for)) {
            return $this->getCountdownTimer(new \DateTime($this->snooze_for), sprintf('snooze-countdown-%d', $this->id));
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

    public function getRating()
    {
        $checked1 = $checked2 = $checked3 = '';
        if ($this->rating == 3) {
            $checked3 = 'checked';
        } elseif ($this->rating == 2) {
            $checked2 = 'checked';
        } elseif ($this->rating == 1) {
            $checked1 = 'checked';
        }

        return '<fieldset class="rate-input-group">
                    <input type="radio" name="rate-' . $this->id . '" id="rate-3-' . $this->id . '" value="3" ' . $checked3 . ' disabled>
                    <label for="rate-3-' . $this->id . '"></label>
                
                    <input type="radio" name="rate-' . $this->id . '" id="rate-2-' . $this->id . '" value="2" ' . $checked2 . ' disabled>
                    <label for="rate-2-' . $this->id . '"></label>
                
                    <input type="radio" name="rate-' . $this->id . '" id="rate-1-' . $this->id . '" value="1" ' . $checked1 . ' disabled>
                    <label for="rate-1-' . $this->id . '"></label>
                </fieldset>';
    }

    public function getPendingAfterCreate()
    {
        $now = new \DateTime();
        $created = new \DateTime($this->created);
        return $this->diffFormat($now->diff($created));
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

    public function getPendingInLastStatus()
    {
        $now = new \DateTime();
        $updated = new \DateTime($this->updated);
        return $this->diffFormat($now->diff($updated));
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
            case self::STATUS_TRASH:
            case self::STATUS_ON_HOLD:
            case self::STATUS_FOLLOW_UP:
                $label = '<span class="label status-label bg-blue">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_SOLD:
            case self::STATUS_BOOKED:
                $label = '<span class="label status-label bg-green">' . self::getStatus($status) . '</span>';
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
    public function getStatusName(bool $label = false) : string
    {
        $statusName = self::STATUS_LIST[$this->status] ?? '-';

        if($label) {
            $class = $this->getStatusLabelClass();
            $statusName = '<span class="label '.$class.'" style="font-size: 13px">' . Html::encode($statusName) . '</span>';
        }

        return $statusName;
    }



    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /*if (empty($this->offset_gmt) && !empty($this->request_ip)) {

            $ctx = stream_context_create(['http' =>
                ['timeout' => 5]  //Seconds
            ]);

            try {
                //echo Yii::$app->params['checkIpURL']; exit;

                $jsonData = file_get_contents(Yii::$app->params['checkIpURL'] . $this->request_ip, false, $ctx);


            } catch (\Throwable $throwable) {
                $jsonData = [];
            }

            if ($jsonData) {

                $data = json_decode($jsonData, true);

                //print_r($data); exit;

                if (isset($data['meta']['code']) && $data['meta']['code'] == '200') {
                    if (isset($data['data']['datetime'])) {
                        $this->offset_gmt = str_replace(':', '.', $data['data']['datetime']['offset_gmt']);
                    }
                    $this->request_ip_detail = json_encode($data['data']);
                    $this->update(false, ['offset_gmt', 'request_ip_detail']);
                }
            }
        }*/

        if ($insert) {
            LeadFlow::addStateFlow($this);
        } else {
            if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {
                LeadFlow::addStateFlow($this);
            }
        }

        if (!$insert) {
            foreach (['updated', 'created'] as $item) {
                if (in_array($item, array_keys($changedAttributes))) {
                    unset($changedAttributes[$item]);
                }
            }
            $flgUnActiveRequest = false;
            if (isset($changedAttributes['adults']) && $changedAttributes['adults'] != $this->adults) {
                $flgUnActiveRequest = true;
            }
            if (isset($changedAttributes['children']) && $changedAttributes['children'] != $this->children) {
                $flgUnActiveRequest = true;
            }
            if (isset($changedAttributes['infants']) && $changedAttributes['infants'] != $this->infants) {
                $flgUnActiveRequest = true;
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
        $leadLog = new LeadLog((new LeadLogMessage()));
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

    public function getClientTime()
    {
        $offset = '';
        $spanId = sprintf('sale-client-time-%d', $this->id);
        if (!empty($this->offset_gmt)) {
            $offset = $this->offset_gmt;
        } elseif (count($this->leadFlightSegments)) {
            $firstSegment = $this->leadFlightSegments[0];
            $airport = Airport::findIdentity($firstSegment->origin);
            if ($airport !== null && !empty($airport->dst)) {
                $offset = $airport->dst;
            }
        }

        if (!empty($offset)) {
            $content = '<span class="sale-client-time" id="' . $spanId . '" data-offset="' . $offset . '"></span>';
            if (!empty($this->leads[0]->country_code)) {
                $info = 'No info!';
                $countryCode = 'N/A';
                if (!empty($this->request_ip)) {
                    if (!empty($this->request_ip_detail)) {
                        $details = json_decode($this->request_ip_detail, true);
                        $countryCode = isset($details['country_code'])
                            ? $details['country_code']
                            : $countryCode;
                    }
                    $info = sprintf('Country: <strong>%s</strong><br>IP: <strong>%s</strong>',
                        strtoupper($countryCode),
                        $this->request_ip);
                }
                $content .= '&nbsp;' . Html::tag('i', '', [
                        'class' => 'flag flag__' . strtolower($countryCode),
                        'style' => 'vertical-align: bottom;',
                        'title' => '',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'right',
                        'data-original-title' => $info,
                        'data-html' => 'true'
                    ]);
            }
            return $content;
        }

        return '';
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
     * @return Reason
     */
    public function lastReason()
    {
        return Reason::find()->orderBy('id desc')->one();
    }

    public function getLastActivity()
    {
        /**
         * @var $note Note
         */
        $note = Note::find()->orderBy('id desc')->one();
        $now = new \DateTime();
        $lastUpdate = new \DateTime($this->updated);
        if ($note !== null) {
            $created = new \DateTime($note->created);
            return ($lastUpdate->getTimestamp() > $created->getTimestamp())
                ? $this->diffFormat($now->diff($lastUpdate))
                : $this->diffFormat($now->diff($created));
        } else {
            return $this->diffFormat($now->diff($lastUpdate));
        }
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

    /**
     * @return Quote[]
     */
    public function getQuotes()
    {
        return Quote::findAll(['lead_id' => $this->id]);
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
            $this->bo_flight_id = (int)$this->bo_flight_id;

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
            $this->additional_information = json_encode($this->additionalInformationForm->attributes);
        }

        parent::afterValidate();
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!empty($this->additional_information)) {
            $additionalInformationFormAttr = json_decode($this->additional_information, true);
            $this->additionalInformationForm->setAttributes($additionalInformationFormAttr);
        }
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
            'flightRequest' => $data,
        ]);

        $sellerContactInfo = EmployeeContactInfo::findOne([
            'employee_id' => $this->employee->id,
            'project_id' => $this->project_id
        ]);
        $credential = [
            'email' => $sellerContactInfo->email_user,
            'password' => $sellerContactInfo->email_pass,
        ];

        if (!empty($template->layout_path)) {
            $body = \Yii::$app->getView()->renderFile($template->layout_path, [
                'project' => $this->project,
                'agentName' => ucfirst($this->employee->username),
                'employee' => $this->employee,
                'sellerContactInfo' => $sellerContactInfo,
                'body' => $body
            ]);
        }

        $subject = ProjectEmailTemplate::getMessageBody($template->subject, [
            'pnr' => $data['pnr'],
        ]);

        $errors = [];
        $isSend = EmailService::send($data['emails'], $this->project, $credential, $subject, $body, $errors);
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

    public function sendEmail($quotes, $email)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        $models = [];
        foreach ($quotes as $quote) {
            $model = Quote::findOne([
                'uid' => $quote
            ]);
            if ($model !== null) {
                $models[] = $model;
            }
        }

        if (empty($models)) {
            $result['errors'][] = sprintf('Quotes not fond. UID: [%s]', implode(', ', $quotes));
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

        $sellerContactInfo = EmployeeContactInfo::findOne([
            'employee_id' => $this->employee->id,
            'project_id' => $this->project_id
        ]);

        $body = Yii::$app->getView()->render($view, [
            'origin' => $origin,
            'destination' => $destination,
            'quotes' => $models,
            'project' => $this->project,
            'agentName' => ucfirst($this->employee->username),
            'employee' => $this->employee,
            'tripType' => $tripType,
            'sellerContactInfo' => $sellerContactInfo
        ]);

        if (!empty($template->layout_path)) {
            $body = \Yii::$app->getView()->renderFile($template->layout_path, [
                'project' => $this->project,
                'agentName' => ucfirst($this->employee->username),
                'employee' => $this->employee,
                'sellerContactInfo' => $sellerContactInfo,
                'body' => $body
            ]);
        }

        $subject = ProjectEmailTemplate::getMessageBody($template->subject, [
            'origin' => $origin,
            'destination' => $destination
        ]);

        $credential = [
            'email' => $sellerContactInfo->email_user,
            'password' => $sellerContactInfo->email_pass,
        ];

        $errors = [];
        $isSend = EmailService::send($email, $this->project, $credential, $subject, $body, $errors);
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
            'agent_name' => $this->employee->username
        ];

        $itinerary = [];
        foreach ($this->leadFlightSegments as $leadFlightSegment) {
            $itinerary[] = [
                'route' => sprintf('%s - %s', $leadFlightSegment->origin, $leadFlightSegment->destination),
                'date' => $leadFlightSegment->departure
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
     * @return string
     */
    public function getStatusLabelClass(): string
    {
        return self::STATUS_CLASS_LIST[$this->status] ?? 'label-default';
    }

}
