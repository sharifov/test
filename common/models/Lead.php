<?php

namespace common\models;

use common\components\EmailService;
use common\components\jobs\QuickSearchInitPriceJob;
use common\components\jobs\UpdateLeadBOJob;
use common\models\local\LeadAdditionalInformation;
use common\models\local\LeadLogMessage;
use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use sales\events\lead\LeadBookedEvent;
use sales\events\lead\LeadCallExpertRequestEvent;
use sales\events\lead\LeadCallStatusChangeEvent;
use sales\events\lead\LeadCreatedCloneEvent;
use sales\events\lead\LeadCreatedEvent;
use sales\events\lead\LeadDuplicateDetectedEvent;
use sales\events\lead\LeadFollowUpEvent;
use sales\events\lead\LeadOwnerChangedEvent;
use sales\events\lead\LeadCountPassengersChangedEvent;
use sales\events\lead\LeadSnoozeEvent;
use sales\events\lead\LeadSoldEvent;
use sales\events\lead\LeadStatusChangedEvent;
use sales\events\lead\LeadTaskEvent;
use sales\helpers\lead\LeadHelper;
use Yii;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;
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
 * @property string $gid
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
 * @property int $bo_flight_id
 * @property string $additional_information
 * @property int $l_answered
 * @property int $l_grade
 * @property int $clone_id
 * @property string $description
 * @property double $final_profit
 * @property double $tips
 * @property int $l_call_status_id
 * @property string $l_pending_delay_dt
 * @property string $l_client_first_name
 * @property string $l_client_last_name
 * @property string $l_client_phone
 * @property string $l_client_email
 * @property string $l_client_lang
 * @property string $l_client_ua
 * @property string $l_request_hash
 * @property int $l_duplicate_lead_id
 * @property double $l_init_price
 * @property string $l_last_action_dt
 * @property int $l_dep_id
 *
 * @property double $finalProfit
 * @property int $quotesCount
 * @property int $leadFlightSegmentsCount
 * @property int $quotesExpertCount
 * @property double $agentProcessingFee
 * @property double $agents_processing_fee
 * @property string $l_client_time
 * @property boolean $enableActiveRecordEvents
 *
 * @property $status_description;
 *
 * @property Call[] $calls
 * @property Email[] $emails
 * @property LeadCallExpert[] $leadCallExperts
 * @property LeadChecklist[] $leadChecklists
 * @property Sms[] $sms
 * @property Quote[] $quotes
 * @property Note[] $notes
 * @property LeadLog[] $leadLogs
 * @property LeadFlightSegment[] $leadFlightSegments
 * @property LeadFlow[] $leadFlows
 * @property LeadPreferences $leadPreferences
 * @property Client $client
 * @property Employee $employee
 * @property Lead $lDuplicateLead
 * @property Lead[] $leads0
 * @property Department $lDep
 * @property Sources $source
 * @property Project $project
 * @property LeadAdditionalInformation[] $additionalInformationForm
 * @property Lead $clone
 * @property ProfitSplit[] $profitSplits
 * @property TipsSplit[] $tipsSplits
 * @property UserConnection[] $userConnections
 *
 */
class Lead extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public const AGENT_PROCESSING_FEE_PER_PAX = 25.0;
    public const PENDING_ALLOW_CALL_TIME_MINUTES = 20; // minutes


    public const TRIP_TYPE_ONE_WAY           = 'OW';
    public const TRIP_TYPE_ROUND_TRIP        = 'RT';
    public const TRIP_TYPE_MULTI_DESTINATION = 'MC';

    public const TRIP_TYPE_LIST = [
        self::TRIP_TYPE_ROUND_TRIP          => 'Round Trip',
        self::TRIP_TYPE_ONE_WAY             => 'One Way',
        self::TRIP_TYPE_MULTI_DESTINATION   => 'Multi destination'
    ];


    public const STATUS_PENDING     = 1;
    public const STATUS_PROCESSING  = 2;
    public const STATUS_REJECT      = 4;
    public const STATUS_FOLLOW_UP   = 5;
    public const STATUS_ON_HOLD     = 8;
    public const STATUS_SOLD        = 10;
    public const STATUS_TRASH       = 11;
    public const STATUS_BOOKED      = 12;
    public const STATUS_SNOOZE      = 13;

    public const STATUS_LIST = [
        self::STATUS_PENDING        => 'Pending',
        self::STATUS_PROCESSING     => 'Processing',
        self::STATUS_REJECT         => 'Reject',
        self::STATUS_FOLLOW_UP      => 'Follow Up',
        self::STATUS_ON_HOLD        => 'Hold On',
        self::STATUS_SOLD           => 'Sold',
        self::STATUS_TRASH          => 'Trash',
        self::STATUS_BOOKED         => 'Booked',
        self::STATUS_SNOOZE         => 'Snooze',
    ];

    public const CLONE_REASONS = [
        1 => 'Group travel',
        2 => 'Alternative credit card',
        3 => 'Different flight',
        4 => 'Flight adjustments',
        0 => 'Other',
    ];

    public const STATUS_MULTIPLE_UPDATE_LIST = [
        self::STATUS_FOLLOW_UP      => self::STATUS_LIST[self::STATUS_FOLLOW_UP],
        self::STATUS_ON_HOLD        => self::STATUS_LIST[self::STATUS_ON_HOLD],
        self::STATUS_PROCESSING     => self::STATUS_LIST[self::STATUS_PROCESSING],
        self::STATUS_TRASH          => self::STATUS_LIST[self::STATUS_TRASH],
        self::STATUS_BOOKED         => self::STATUS_LIST[self::STATUS_BOOKED],
        self::STATUS_SNOOZE         => self::STATUS_LIST[self::STATUS_SNOOZE],
    ];

    public const STATUS_CLASS_LIST = [
        self::STATUS_PENDING        => 'll-pending',
        self::STATUS_PROCESSING     => 'll-processing',
        self::STATUS_FOLLOW_UP      => 'll-follow_up',
        self::STATUS_ON_HOLD        => 'll-on_hold',
        self::STATUS_SOLD           => 'll-sold',
        self::STATUS_TRASH          => 'll-trash',
        self::STATUS_BOOKED         => 'll-booked',
        self::STATUS_SNOOZE         => 'll-snooze',
    ];


    public const CABIN_ECONOMY      = 'E';
    public const CABIN_BUSINESS     = 'B';
    public const CABIN_FIRST        = 'F';
    public const CABIN_PREMIUM      = 'P';

    public const CABIN_LIST = [
        self::CABIN_ECONOMY     => 'Economy',
        self::CABIN_PREMIUM     => 'Premium eco',
        self::CABIN_BUSINESS    => 'Business',
        self::CABIN_FIRST       => 'First',
    ];

    public const
        DIV_GRID_WITH_OUT_EMAIL = 1,
        DIV_GRID_WITH_EMAIL = 2,
        DIV_GRID_SEND_QUOTES = 3,
        DIV_GRID_IN_SNOOZE = 4;

    public const CALL_STATUS_NONE       = 0;
    public const CALL_STATUS_READY      = 1;
    public const CALL_STATUS_PROCESS    = 2;
    public const CALL_STATUS_CANCEL     = 3;
    public const CALL_STATUS_DONE       = 4;
    public const CALL_STATUS_QUEUE      = 5;

    public const CALL_STATUS_LIST = [
        self::CALL_STATUS_NONE      => 'None',
        self::CALL_STATUS_READY     => 'Ready',
        self::CALL_STATUS_PROCESS   => 'Process',
        self::CALL_STATUS_CANCEL    => 'Cancel',
        self::CALL_STATUS_DONE      => 'Done',
        self::CALL_STATUS_QUEUE     => 'Queue',
    ];


    public const SCENARIO_API = 'scenario_api';
    public const SCENARIO_MULTIPLE_UPDATE = 'scenario_multiple_update';

    public $additionalInformationForm;
    public $status_description;
    public $totalProfit;
    public $splitProfitPercentSum = 0;
    public $totalTips;
    public $splitTipsPercentSum = 0;

    public $finalProfit = 0;
    public $agentProcessingFee = 0.00;
    public $l_client_time;

    public $enableActiveRecordEvents = true;

    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
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

            [['trip_type', 'cabin'], 'required', 'on' => self::SCENARIO_API],
            [['adults', 'children', 'source_id'], 'required', 'on' => self::SCENARIO_API], //'except' => self::SCENARIO_API],
            [['adults'], 'integer', 'min' => 1, 'on' => self::SCENARIO_API],

            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'rating', 'bo_flight_id', 'l_grade', 'clone_id', 'l_call_status_id', 'l_duplicate_lead_id', 'l_dep_id'], 'integer'],
            [['adults', 'children', 'infants'], 'integer', 'max' => 9],

            [['notes_for_experts', 'request_ip_detail', 'l_client_ua'], 'string'],

            [['created', 'updated', 'snooze_for', 'called_expert', 'additional_information', 'l_pending_delay_dt', 'l_last_action_dt'], 'safe'],

            [['final_profit', 'tips', 'agents_processing_fee', 'l_init_price'], 'number'],
            [['uid', 'request_ip', 'offset_gmt', 'discount_id', 'description'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],
            [['gid', 'l_request_hash'], 'string', 'max' => 32],
            [['l_client_first_name', 'l_client_last_name'], 'string', 'max' => 50],
            [['l_client_phone'], 'string', 'max' => 20],
            [['l_client_email'], 'string', 'max' => 160],
            [['l_client_lang'], 'string', 'max' => 5],
            [['gid'], 'unique'],
            [['l_answered'], 'boolean'],
            [['status_description'], 'string'],

            [['clone_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['clone_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['l_duplicate_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['l_duplicate_lead_id' => 'id']],
            [['l_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['l_dep_id' => 'dep_id']],
        ];
    }

    /**
     * @param $clientId
     * @param $clientFirstName
     * @param $clientLastName
     * @param $cabin
     * @param $adults
     * @param $children
     * @param $infants
     * @param $requestIp
     * @param $sourceId
     * @param $projectId
     * @param $notesForExperts
     * @param $clientPhone
     * @param $clientEmail
     * @param $depId
     * @return Lead
     */
    public static function create(
        $clientId,
        $clientFirstName,
        $clientLastName,
        $cabin,
        $adults,
        $children,
        $infants,
        $requestIp,
        $sourceId,
        $projectId,
        $notesForExperts,
        $clientPhone,
        $clientEmail,
        $depId
    ): self
    {
        $lead = new static();
        $lead->client_id = $clientId;
        $lead->l_client_first_name = $clientFirstName;
        $lead->l_client_last_name = $clientLastName;
        $lead->cabin = $cabin;
        $lead->adults = $adults;
        $lead->children = $children;
        $lead->infants = $infants;
        $lead->request_ip = $requestIp;
        $lead->source_id = $sourceId;
        $lead->project_id = $projectId;
        $lead->notes_for_experts = $notesForExperts;
        $lead->uid = self::generateUid();
        $lead->gid = self::generateGid();
        $lead->l_client_phone = $clientPhone;
        $lead->l_client_email = $clientEmail;
        $lead->l_dep_id = $depId;
        $lead->status = self::STATUS_PENDING;
        $lead->recordEvent(new LeadCreatedEvent($lead));
        return $lead;
    }

    /**
     * @param int $ownerId
     * @param string|null $description
     * @return Lead
     */
    public function createClone(int $ownerId, ?string $description): self
    {
        $clone = new static();
        $clone->attributes = $this->attributes;
        $clone->description = $description;
        $clone->notes_for_experts = null;
        $clone->rating = 0;
        $clone->additional_information = null;
        $clone->l_answered = 0;
        $clone->l_grade = 0;
        $clone->snooze_for = null;
        $clone->called_expert = false;
        $clone->created = null;
        $clone->updated = null;
        $clone->tips = 0;
        $clone->uid = self::generateUid();
        $clone->gid = self::generateGid();
        $clone->status = self::STATUS_PENDING;
        $clone->clone_id = $this->id;
        $clone->employee_id = null;
        $clone->take($ownerId);
        $clone->recordEvent(new LeadCreatedCloneEvent($clone));
        return $clone;
    }

    /**
     * @return string
     */
    private static function generateUid(): string
    {
        return uniqid();
    }

    /**
     * @return string
     */
    private static function generateGid(): string
    {
        return md5(uniqid('', true));
    }

    /**
     * @param int $value
     */
    public function changeRating(int $value): void
    {
        $this->setRating($value);
    }

    /**
     * @param int $rating
     */
    private function setRating(int $rating): void
    {
        if ($rating < 0 || $rating > 3) {
            throw new InvalidArgumentException('Invalid rating!');
        }
        $this->rating = $rating;
    }

    /**
     * @return bool
     */
    public function isAnswered(): bool
    {
        return $this->l_answered ? true : false;
    }

    public function changeAnswered(): void
    {
        if ($this->isAnswered()) {
            $this->setAnswered(false);
        } else {
            $this->setAnswered(true);
        }
    }

    /**
     * @param bool $value
     */
    private function setAnswered(bool $value): void
    {
        if ($this->l_answered !== $value) {
            $this->recordEvent(new LeadTaskEvent($this), LeadTaskEvent::class);
        }
        $this->l_answered = $value;
    }

    public function sendCallExpertRequest(): void
    {
        if (in_array($this->status, [self::STATUS_TRASH, self::STATUS_FOLLOW_UP, self::STATUS_SNOOZE, self::STATUS_PROCESSING], true)) {
            $this->recordEvent(new LeadCallExpertRequestEvent($this));
        }
        $this->setCalledExpert(true);
    }

    /**
     * @return bool
     */
    public function isCalledExpert(): bool
    {
        return $this->called_expert == 1 ? true : false;
    }

    /**
     * @param bool $value
     */
    public function setCalledExpert(bool $value): void
    {
        $this->called_expert = $value;
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function isAlreadyTakenUser(int $userId): bool
    {
        return $this->isOwner($userId) && $this->isProcessing();
    }

    /**
     * @param int $userId
     */
    public function take(int $userId): void
    {
        if ($this->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if ($this->isAlreadyTakenUser($userId)) {
            throw new \DomainException('Lead is already taken to this user!');
        }

        if (!$this->isAvailableToTake()) {
            throw new \DomainException('Lead is unavailable to "Take" now!');
        }

        $this->assign($userId, self::STATUS_PROCESSING);
    }

    /**
     * @param int $userId
     */
    public function takeOver(int $userId): void
    {
        if ($this->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if ($this->isAlreadyTakenUser($userId)) {
            throw new \DomainException('Lead is already taken to this user!');
        }

        if (!$this->isAvailableToTakeOver()) {
            throw new \DomainException('Lead is unavailable to "Take Over" now!');
        }

        $this->assign($userId, self::STATUS_PROCESSING);
    }

    /**
     * @param int|null $userId
     * @param int $status
     */
    private function assign(?int $userId, int $status): void
    {
        if ($this->status === $status && $this->isOwner($userId)) {
            throw new \DomainException('Lead is already assigned to this user!');
        }
//        $this->recordEvent(new LeadAssignedEvent($this, $this->employee_id, $userId, $this->status, $status));
        $this->setStatus($status);
        $this->setOwner($userId);
    }

    public function isGetOwner(): bool
    {
        return $this->employee_id ? true : false;
    }

    /**
     * @param int|null $userId
     * @return bool
     */
    private function isOwner(?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return $this->employee && $this->employee->id === $userId;
    }

    /**
     * @param int|null $userId
     */
    private function setOwner(?int $userId): void
    {
        if (!$this->isOwner($userId)) {
            $this->recordEvent(new LeadOwnerChangedEvent($this, $this->employee_id, $userId));
            if ($this->isProcessing()) {
                $this->recordEvent(new LeadTaskEvent($this), LeadTaskEvent::class);
            }
        }
        $this->employee_id = $userId;
    }

    /**
     * @param int $status
     */
    private function setStatus(int $status): void
    {
        if (!array_key_exists($status, self::STATUS_LIST)) {
            throw new InvalidArgumentException('Invalid Status');
        }
        if ($this->status !== $status) {

            $this->recordEvent(new LeadStatusChangedEvent($this, $this->status, $status, $this->employee_id));

            if ($status === self::STATUS_PROCESSING) {
                $this->recordEvent(new LeadTaskEvent($this), LeadTaskEvent::class);
            }

            if ($this->isCalledExpert() && in_array($status, [self::STATUS_TRASH, self::STATUS_FOLLOW_UP, self::STATUS_SNOOZE, self::STATUS_PROCESSING], true)) {
                $this->recordEvent(new LeadCallExpertRequestEvent($this));
            }
        }
        $this->status = $status;
    }

    /**
     * @param int $status
     */
    private function setCallStatus(int $status): void
    {
        if (!array_key_exists($status, self::CALL_STATUS_LIST)) {
            throw new InvalidArgumentException('Invalid Call Status');
        }
        if ($this->l_call_status_id !== $status) {
            $this->recordEvent(new LeadCallStatusChangeEvent($this, $this->l_call_status_id, $status, $this->employee_id));
        }
        $this->l_call_status_id = $status;
    }

    /**
     * @param string $cabin
     * @param int $adults
     * @param int $children
     * @param int $infants
     */
    public function editItinerary(string $cabin, int $adults, int $children, int $infants): void
    {
        $this->cabin = $cabin;
        $this->editPassengers($adults, $children, $infants);
    }

    /**
     * @param int $adults
     * @param int $children
     * @param int $infants
     */
    public function editPassengers(int $adults, int $children, int $infants): void
    {
        if ($this->adults !== $adults || $this->children !== $children || $this->infants !== $infants) {
            $this->recordEvent(new LeadCountPassengersChangedEvent($this));
        }
        $this->adults = $adults;
        $this->children = $children;
        $this->infants = $infants;
    }

    /**
     * @param string|null $type
     */
    public function setTripType(string $type = null): void
    {
        if ($type) {
            $list = LeadHelper::tripTypeList();
            if (isset($list[$type])) {
                $this->trip_type = $type;
                return;
            }
        }
        $this->trip_type = '';
    }

    public function sold(): void
    {
        if ($this->isSold()) {
            throw new \DomainException('Lead is already sold!');
        }
        $this->setStatus(self::STATUS_SOLD);
        $this->recordEvent(new LeadSoldEvent($this));
    }

    /**
     * @return bool
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    public function booked(): void
    {
        if ($this->isBooked()) {
            throw new \DomainException('Lead is already booked!');
        }
        $this->setStatus(self::STATUS_BOOKED);
        $this->recordEvent(new LeadBookedEvent($this));
    }

    /**
     * @return bool
     */
    public function isBooked(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    /**
     * @param $snoozeFor
     */
    public function snooze($snoozeFor): void
    {
        if ($this->isSnooze()) {
            throw new \DomainException('Lead is already snooze!');
        }
        $this->setStatus(self::STATUS_SNOOZE);
        $snoozeFor = $snoozeFor ? date('Y-m-d H:i:s', strtotime($snoozeFor)) : null;
        $this->snooze_for = $snoozeFor;
        $this->recordEvent(new LeadSnoozeEvent($this));
    }

    /**
     * @return bool
     */
    public function isSnooze(): bool
    {
        return $this->status === self::STATUS_SNOOZE;
    }

    public function followUp(): void
    {
        if ($this->isFollowUp()) {
            throw new \DomainException('Lead is already follow up!');
        }
        $this->recordEvent(new LeadFollowUpEvent($this, $this->employee_id));
        $this->assign(null, self::STATUS_FOLLOW_UP);
    }

    /**
     * @return bool
     */
    public function isFollowUp(): bool
    {
        return $this->status === self::STATUS_FOLLOW_UP;
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function trash(): void
    {
        if ($this->isTrash()) {
            throw new \DomainException('Lead is already trash!');
        }
        $this->setStatus(self::STATUS_TRASH);
    }

    /**
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->status === self::STATUS_TRASH;
    }

    /**
     * @return bool
     */
    public function isOnHold(): bool
    {
        return $this->status === self::STATUS_ON_HOLD;
    }

    public function reject(): void
    {
        $this->setStatus(self::STATUS_REJECT);
    }

    public function callProcessing(): void
    {
        $this->setCallStatus(self::CALL_STATUS_PROCESS);
    }

    /**
     * @return bool
     */
    public function isCallProcessing(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_PROCESS;
    }

    public function callReady(): void
    {
        $this->setCallStatus(self::CALL_STATUS_READY);
    }

    /**
     * @return bool
     */
    public function isCallReady(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_READY;
    }

    public function callDone()
    {
        $this->setCallStatus(self::CALL_STATUS_DONE);
    }

    /**
     * @return bool
     */
    public function isCallDone(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_DONE;
    }

    /**
     * @return bool
     */
    public function isEmptyRequestHash(): bool
    {
        return $this->l_request_hash ? false : true;
    }

    public function setRequestHash(string $hash): void
    {
        $this->l_request_hash = $hash;
    }

    public function setDuplicate(int $originId): void
    {
        $this->l_duplicate_lead_id = $originId;
        $this->trash();
        $this->recordEvent(new LeadDuplicateDetectedEvent($this));
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_BOOKED, self::STATUS_SOLD], true);
    }

    /**
     * @return bool
     */
    public function isAvailableToTakeOver(): bool
    {
        return in_array($this->status, [self::STATUS_ON_HOLD, self::STATUS_PROCESSING], true);
    }

    /**
     * @return bool
     */
    public function isAvailableToTake(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_FOLLOW_UP, self::STATUS_SNOOZE], true);
    }

    /**
     * @param int $employeeId
     * @return bool
     */
    public function canAgentEdit(int $employeeId): bool
    {
        return $this->status === self::STATUS_PROCESSING && ($this->employee && $this->employee->id === $employeeId);
    }

    /**
     * @param array $supervisionGroups
     * @return bool
     */
    public function canSupervisionEdit(array $supervisionGroups): bool
    {
        if (!in_array($this->status, [self::STATUS_PROCESSING, self::STATUS_PENDING, self::STATUS_FOLLOW_UP, self::STATUS_SNOOZE], true)) {
            return false;
        }
        $employeeGroups = $this->employee ? array_keys($this->employee->userGroupList) : [];
        foreach (array_keys($supervisionGroups) as $group) {
            if (in_array($group, $employeeGroups)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canAdminEdit(): bool
    {
        return in_array($this->status, [self::STATUS_PROCESSING, self::STATUS_PENDING, self::STATUS_FOLLOW_UP, self::STATUS_SNOOZE], true);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'uid' => 'UID',
            'gid' => 'GID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'status' => 'Status',
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
            'bo_flight_id' => '(BO) Flight ID',
            'agents_processing_fee' => 'Agents Processing Fee',
            'origin_country' => 'Origin Country code',
            'destination_country' => 'Destination Country code',
            'l_call_status_id' => 'Call status',
            'l_pending_delay_dt' => 'Pending delay',

            'l_client_first_name' => 'Client First Name',
            'l_client_last_name' => 'Client Last Name',
            'l_client_phone' => 'Client Phone',
            'l_client_email' => 'Client Email',
            'l_client_lang' => 'Client Lang',
            'l_client_ua' => 'Client UserAgent',
            'l_request_hash' => 'Request Hash',
            'l_duplicate_lead_id' => 'Duplicate Lead ID',

            'l_init_price' => 'Init Price',
            'l_last_action_dt' => 'Last Action',
            'l_dep_id' => 'Department ID',

        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated', 'l_last_action_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated', 'l_last_action_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'l_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadCallExperts()
    {
        return $this->hasMany(LeadCallExpert::class, ['lce_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadChecklists(): ActiveQuery
    {
        return $this->hasMany(LeadChecklist::class, ['lc_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLDuplicateLead(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'l_duplicate_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeads0()
    {
        return $this->hasMany(self::class, ['l_duplicate_lead_id' => 'id']);
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


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms(): ActiveQuery
    {
        return $this->hasMany(Sms::class, ['s_lead_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotes(): ActiveQuery
    {
        return $this->hasMany(Note::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipsSplits(): ActiveQuery
    {
        return $this->hasMany(TipsSplit::class, ['ts_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuotes(): ActiveQuery
    {
        return $this->hasMany(Quote::class, ['lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTsUsers(): ActiveQuery
    {
        return $this->hasMany(Employee::class, ['id' => 'ts_user_id'])->viaTable('tips_split', ['ts_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserConnections(): ActiveQuery
    {
        return $this->hasMany(UserConnection::class, ['uc_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlightSegments(): ActiveQuery
    {
        return $this->hasMany(LeadFlightSegment::class, ['lead_id' => 'id']);
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
    public function getClone(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'clone_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfitSplits(): ActiveQuery
    {
        return $this->hasMany(ProfitSplit::class, ['ps_lead_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource(): ActiveQuery
    {
        return $this->hasOne(Sources::class, ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }


    /**
     * @return int
     */
    public function getQuotesCount(): int
    {
        return $this->hasMany(Quote::class, ['lead_id' => 'id'])->count();
    }

    /**
     * @return int
     */
    public function getQuotesExpertCount(): int
    {
        return $this->hasMany(Quote::class, ['lead_id' => 'id'])->where(['created_by_seller' => 0])->count();
    }

    /**
     * @return int
     */
    public function getLeadFlightSegmentsCount(): int
    {
        return $this->hasMany(LeadFlightSegment::class, ['lead_id' => 'id'])->count();
    }

    /**
     * @return int
     */
    public function updateLastAction() : int
    {
        return self::updateAll(['l_last_action_dt' => date('Y-m-d H:i:s')], ['id' => $this->id]);
    }

//
//
//    public static function getBadgesSingleQuery()
//    {
//        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
//
//        if(empty($projectIds)){
//            $projectIds[] = 0;
//        }
//
//
//        $userId = Yii::$app->user->id;
//        $created = '';
//        $employee = '';
//        if (Yii::$app->user->identity->canRole('agent')) {
//            $employee = ' AND employee_id = ' . $userId;
//        }
//
//        $sold = '';
//
//        if (Yii::$app->user->identity->canRole('supervision')) {
//            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $userId]);
//            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
//            $resEmp = $subQuery->createCommand()->queryAll();
//            $empArr = [];
//            if ($resEmp) {
//                foreach ($resEmp as $entry) {
//                    $empArr[] = $entry['ugs_user_id'];
//                }
//            }
//
//            if (!empty($empArr)) {
//                $employee = 'AND leads.employee_id IN (' . implode(',', $empArr) . ')';
//            }
//
//        }
//
//        if (Yii::$app->user->identity->canRole('agent')) {
//            $sold = ' AND (employee_id = ' . $userId.' OR ps.ps_user_id ='.$userId.' OR ts.ts_user_id ='.$userId.')';
//        }
//        $default = implode(',', [
//            self::STATUS_PROCESSING,
//            self::STATUS_ON_HOLD,
//            self::STATUS_SNOOZE
//        ]);
//
//        $select = [
//            'pending' => 'COUNT(DISTINCT CASE WHEN status IN (:inbox) THEN leads.id ELSE NULL END)',
//            'inbox' => 'COUNT(DISTINCT CASE WHEN status IN (:inbox) THEN leads.id ELSE NULL END)',
//            'follow-up' => 'COUNT(DISTINCT CASE WHEN status IN (:followup) ' . $created . '  THEN leads.id ELSE NULL END)',
//            'booked' => 'COUNT(DISTINCT CASE WHEN status IN (:booked) ' . $created . ' THEN leads.id ELSE NULL END)',
//            //'sold' => 'COUNT(DISTINCT CASE WHEN status IN (:sold) ' . $created . $sold .$employee . ' THEN leads.id ELSE NULL END)',
//            'sold' => '(SELECT COUNT(leads.id) FROM leads
//                        LEFT JOIN '.ProfitSplit::tableName().' ps ON ps.ps_lead_id = leads.id
//                        LEFT JOIN '.TipsSplit::tableName().' ts ON ts.ts_lead_id = leads.id
//                        WHERE leads.status IN (:sold) '.$created . $sold .$employee.'
//                        AND leads.project_id IN ('.implode(',', $projectIds).'))',
//            'processing' => 'COUNT(DISTINCT CASE WHEN status IN (' . $default . ') ' . $employee . ' THEN leads.id ELSE NULL END)'
//        ];
//
//        /*if (Yii::$app->user->identity->role != 'agent') {
//            //$select['trash'] = 'COUNT(DISTINCT CASE WHEN status IN (' . self::STATUS_TRASH . ') ' . $created . $employee . ' THEN leads.id ELSE NULL END)';
//            //$select['pending'] = 'COUNT(DISTINCT CASE WHEN status IN (:inbox) THEN leads.id ELSE NULL END)';
//
//            $select['duplicate'] = 'COUNT(DISTINCT CASE WHEN status IN (' . self::STATUS_TRASH . ') ' . $created . $employee . ' THEN leads.id ELSE NULL END)';
//        }*/
//
//        if (Yii::$app->user->identity->canRole('admin')) {
//            $select['pending'] = 'COUNT(DISTINCT CASE WHEN status IN (:pending) THEN leads.id ELSE NULL END)';
//        }
//
//        $query = self::find()
//            //->cache(600)
//            ->select($select)
//            //->leftJoin(ProfitSplit::tableName().' ps','ps.ps_lead_id = leads.id')
//            //->leftJoin(TipsSplit::tableName().' ts','ts.ts_lead_id = leads.id')
//            ->andWhere(['IN', 'project_id', $projectIds])
//            ->addParams([':inbox' => self::STATUS_PENDING,
//                ':pending' => self::STATUS_PENDING,
//                ':followup' => self::STATUS_FOLLOW_UP,
//                ':booked' => self::STATUS_BOOKED,
//                ':sold' => self::STATUS_SOLD,
//            ])
//            ->limit(1);
//
//
//        //echo $query->createCommand()->getRawSql();die;
//
//        $db = Yii::$app->db;
//        $duration = 0;     // cache query results for 60 seconds.
//        $dependency = new DbDependency();
//        $dependency->sql = 'SELECT MAX(id) FROM leads';
//
//
//        $result = $db->cache(function ($db) use ($query) {
//            return $query->createCommand()->queryOne();
//        }, $duration, $dependency);
//
//        //$result = $query->createCommand()->queryOne();
//
//
//        $result['duplicate'] = '';
//
//        if (Yii::$app->user->identity->canRoles(['admin', 'qa'])) {
//            $result['duplicate'] = self::find()->where(['IS NOT', 'l_duplicate_lead_id', null])->count() ?: '' ;
//        }
//
//        return $result; // $query->createCommand()->queryOne();
//    }

    /**
     * @return array
     */
    public static function getLeadQueueType(): array
    {
        return [
            'pending',
            'inbox', 'follow-up', 'processing',
            'processing-all', 'booked', 'sold', 'trash'
        ];
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

    public function getStatusDate($status)
    {
        $flow = LeadFlow::find()->where(['lead_id' => $this->id ,'status' => $status])->one();
        if($flow){
            return $flow['created'];
        }

        return null;
    }

    /**
     * @param null $cabin
     * @return mixed|null
     */
    public static function getCabin($cabin = null)
    {
        return self::CABIN_LIST[$cabin] ?? $cabin;
    }

    /**
     * @return array
     */
    public static function getCabinList(): array
    {
        return self::CABIN_LIST;
    }

    /**
     * @return mixed|string
     */
    public function getCabinClassName()
    {
        return self::CABIN_LIST[$this->cabin] ?? $this->cabin;
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

    /**
     * @param $id
     * @param $snooze_for
     * @return string
     * @throws \Exception
     */
    public static function getSnoozeCountdown($id, $snooze_for): string
    {
        if (!empty($snooze_for)) {
            return self::getCountdownTimer(new \DateTime($snooze_for), sprintf('snooze-countdown-%d', $id));
        }
        return '-';
    }

    /**
     * @param \DateTime $expired
     * @param $spanId
     * @return string
     */
    public static function getCountdownTimer(\DateTime $expired, $spanId): string
    {
        //var expired = moment.tz("' . $expired->format('Y-m-d H:i:s') . '", "UTC");

        return '<span id="' . $spanId . '" data-toggle="tooltip" data-placement="right" data-original-title="' . $expired->format('Y-m-d H:i') . '"></span>
                <script type="text/javascript">
                    $("#' . $spanId . '").countdown("' . $expired->format('Y/m/d H:i:s') . '", function(event) {
                        if (event.elapsed == false) {
                            $(this).text(event.strftime(\'%Dd %Hh %Mm\'));
                        } else {
                            $(this).text(event.strftime(\'On Wake\')).addClass(\'text-success\');
                        }
                    });
                </script>';
    }

    /**
     * @param $updated
     * @return string
     * @throws \Exception
     */
    public static function getLastActivity($updated): string
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

    /**
     * @param \DateInterval $interval
     * @return string
     */
    public static function diffFormat(\DateInterval $interval)
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
        if (!Yii::$app->user->identity->canRole('admin')) {
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
     * @return array
     */
    public function updateIpInfo(): array
    {

        $out = ['error' => false, 'data' => []];

        if (empty($this->offset_gmt) && !empty($this->request_ip)) {

            $ip = $this->request_ip; //'217.26.162.22';
            $key = Yii::$app->params['ipinfodb_key'] ?? '';
            $url = 'http://api.ipinfodb.com/v3/ip-city/?format=json&key=' . $key . '&ip=' . $ip;

            $ctx = stream_context_create(['http' =>
                ['timeout' => 5]  //Seconds
            ]);

            try {
                $jsonData = file_get_contents($url, false, $ctx);

                if ($jsonData) {
                    $data = @json_decode($jsonData, true);


                    if ($data && isset($data['timeZone'])) {

                        if(isset($data['statusCode'])) {
                            unset($data['statusCode']);
                        }

                        if(isset($data['statusMessage'])) {
                            unset($data['statusMessage']);
                        }

                        $this->offset_gmt = $data['timeZone'];
                        $this->request_ip_detail = json_encode($data);

                        self::updateAll(['offset_gmt' => $this->offset_gmt, 'request_ip_detail' => $this->request_ip_detail], ['id' => $this->id]);

                        $out['data'] = $data;
                    }

                }

            } catch (\Throwable $throwable) {
                $out['error'] = $throwable->getMessage();

                if(!$this->offset_gmt && $this->leadFlightSegments) {
                    $firstSegment = $this->leadFlightSegments[0];
                    $airport = Airport::findIdentity($firstSegment->origin);
                    if ($airport && $airport->dst) {
                        $offset = $airport->dst;
                        if(is_numeric($offset)) {

                            $offsetStr = null;

                            if($offset > 0) {
                                if($offset < 10) {
                                    $offsetStr = '+0'.$offset.':00';
                                } else {
                                    $offsetStr = '+'.$offset.':00';
                                }
                            }

                            if($offset < 0) {
                                if($offset > -10) {
                                    $offsetStr = '-0'.$offset.':00';
                                } else {
                                    $offsetStr = '-'.$offset.':00';
                                }
                            }

                            if($offset == 0) {
                                $offsetStr = '-00:00';
                            }

                            if($offsetStr) {
                                $this->offset_gmt = $offsetStr;
                                self::updateAll(['offset_gmt' => $this->offset_gmt], ['id' => $this->id]);
                            }

                        }

                    }
                }
            }
        }

        return $out;
    }


    /**
     * @param null $type
     * @param null $employee_id
     * @param null $employee2_id
     * @param null $lead
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendNotification($type = null, $employee_id = null, $employee2_id = null, $lead = null): bool
    {
        $isSend = false;

        $host = \Yii::$app->params['url_address'];

        if ($type && $employee_id && isset(Yii::$app->params['email_from']['sales'])) {
            $user = Employee::findOne($employee_id);

            if($employee2_id) {
                $user2 = Employee::findOne($employee2_id);
            } else {
                $user2 = null;
            }

            if ($user && $user->email) {

                //$swiftMailer = Yii::$app->mailer2;

                $userName = $user->username;

                if ($user2) {
                    $userName2 = $user2->username;
                } else {
                    $userName2 = '-';
                }

                $body = 'Hi!';
                $subject = 'Default subject';

                if ($type === 'reassigned-lead') {

                    $body = Yii::t('email', "Attention!
Your Lead (ID: {lead_id}) has been reassigned to another agent ({name2}).
{url}",
                        [
                            'name' => $userName,
                            'name2' => $userName2,
                            'url' => $host . '/lead/view/' . $this->gid,
                            'lead_id' => $this->id,
                            'br' => "\r\n"
                        ]);

                    $subject = Yii::t('email', 'Lead-{id} reassigned to ({username})', ['id' => $this->id, 'username' => $userName2]);

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

                    $body = Yii::t('email', "Booked quote with UID : {quote_uid},
Source: {name},
Lead ID: {lead_id} ({url})
{name} made \${profit} on {airline} to {destination}",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/view/' . $this->gid,
                            'lead_id' => $this->id,
                            'quote_uid' => $quote ? $quote->uid : '-',
                            'destination' => $flightSegment ? $flightSegment->destination : '-',
                            'airline' => $airlineName,
                            'profit' => $profit,
                            'br' => "\r\n"
                        ]);

                    $subject = Yii::t('email', 'Lead-{id} to SOLD', ['id' => $this->id]);
                } elseif ($type === 'lead-status-booked') {


                    $subject = Yii::t('email', 'Lead-{id} to BOOKED', ['id' => $this->id]);
                    $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

                    $body = Yii::t('email', "Your Lead (ID: {lead_id}) has been changed status to BOOKED!
Booked quote UID: {quote_uid}
{url}",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/view/' . $this->gid,
                            'lead_id' => $this->id,
                            'quote_uid' => $quote ? $quote->uid : '-',
                            'br' => "\r\n"
                        ]);


                } elseif ($type === 'lead-status-snooze') {

                    $subject = Yii::t('email', "Lead-{id} to SNOOZE", ['id' => $this->id]);
                    $body = Yii::t('email', "Your Lead (ID: {lead_id}) has been changed status to SNOOZE!
Snooze for: {datetime}.
Reason: {reason}
{url}",
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/view/' . $this->gid,
                            'datetime' => Yii::$app->formatter->asDatetime(strtotime($this->snooze_for)),
                            'reason' => $this->status_description ?: '-',
                            'lead_id' => $this->id,
                            'br' => "\r\n"
                        ]);


                } elseif ($type === 'lead-status-follow-up') {

                    $subject = Yii::t('email', "Lead-{id} to FOLLOW-UP", ['id' => $this->id]);
                    $body = Yii::t('email', 'Your Lead (ID: {lead_id}) has been changed status to FOLLOW-UP!
Reason: {reason}
{url}',
                        [
                            'name' => $userName,
                            'url' => $host . '/lead/view/' . $this->gid,
                            'reason' => $this->status_description ?: '-',
                            'lead_id' => $this->id,
                            'br' => "\r\n"
                        ]);

                }


                try {
                    /*$isSend = $swiftMailer
                        ->compose()//'sendDeliveryEmailForClient', ['order' => $this])
                        ->setTo($user->email)
                        ->setBcc(Yii::$app->params['email_to']['bcc_sales'])
                        ->setFrom(Yii::$app->params['email_from']['sales'])
                        ->setSubject($subject)
                        ->setTextBody($body)
                        ->send();*/

                    //Notifications::create()

                    $isSend = Notifications::create($user->id, $subject, $body, Notifications::TYPE_INFO, true);
                    Notifications::socket($user->id, null, 'getNewNotification', [], true);


                    if (!$isSend) {
                        Yii::warning('Not send Notification to UserID:' . $user->id . ' - Lead Id: ' . $this->id, 'Lead:sendNotification:' . $type);
                    }

                } catch (\Throwable $e) {
                    Yii::error($user->email . ' ' . $e->getMessage(), 'Lead:sendNotification:Notifications:create');
                }

            } else {
                Yii::warning('Not found employee (' . $employee_id . ')', 'Lead:sendNotification:' . $type);
            }
        } else {
            Yii::warning("type = $type, employee_id = $employee_id, employee2_id = $employee2_id", 'Lead:sendNotification:' . $type);
        }

        return $isSend;
    }

    /**
     * @param Lead $lead
     * @return bool
     */
    public function sendClonedEmail(Lead $lead): bool
    {
        $isSend = false;

        $host = \Yii::$app->params['url_address'];

        //$swiftMailer = Yii::$app->mailer2;
        $user = Employee::findOne($lead->employee_id);

        if (!empty($user)) {
            $agent = $user->username;
            $subject = Yii::t('email', "Cloned Lead-{id} by {agent}", ['id' => $lead->clone_id, 'agent' => $agent]);
            $body = Yii::t('email', "Agent {agent} cloned lead {clone_id} with reason [{reason}], url: {cloned_url}.
New lead {lead_id}
{url}",
                [
                    'agent' => $agent,
                    'url' => $host . '/lead/view/' . $lead->gid,
                    'cloned_url' => $host . '/lead/view/' . ($lead->clone ? $lead->clone->gid : $lead->gid),
                    'reason' => $lead->description,
                    'lead_id' => $lead->id,
                    'clone_id' => $lead->clone_id,
                    'br' => "\r\n"
                ]);

            //$emailTo = Yii::$app->params['email_to']['bcc_sales'];

            try {

                $isSend = Notifications::create($user->id, $subject, $body, Notifications::TYPE_INFO, true);
                Notifications::socket($user->id, null, 'getNewNotification', [], true);

                /*$isSend = $swiftMailer
                    ->compose()
                    ->setTo($emailTo)
                    ->setFrom(Yii::$app->params['email_from']['sales'])
                    ->setSubject($subject)
                    ->setTextBody($body)
                    ->send();*/

                if (!$isSend) {
                    Yii::warning('Not send Notification to UserID:' . $user->id . ' - Lead Id: ' . $this->id, 'Lead:sendClonedEmail:Notifications::create');
                }

            } catch (\Throwable $e) {
                Yii::error($user->id . ' ' . $e->getMessage(), 'Lead:sendClonedEmail:Notifications::create');
            }
        } else {
            Yii::warning('Not found employee (' . $lead->employee_id . ')', 'Lead:sendClonedEmail');
        }
        //}

        return $isSend;
    }

    public function disableAREvents(): void
    {
        $this->enableActiveRecordEvents = false;
    }

    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);

        if ($this->enableActiveRecordEvents) {

            if ($insert) {
                LeadFlow::addStateFlow($this);

                /*$job = new QuickSearchInitPriceJob();
                $job->lead_id = $this->id;
                $jobId = Yii::$app->queue_job->push($job);*/

                //Yii::info('Lead: ' . $this->id . ', QuickSearchInitPriceJob: '.$jobId, 'info\Lead:afterSave:QuickSearchInitPriceJob');

            } else {




                if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {
                    LeadFlow::addStateFlow($this);

                    if($this->called_expert && ($this->status == self::STATUS_TRASH || $this->status == self::STATUS_FOLLOW_UP || $this->status == self::STATUS_SNOOZE || $this->status == self::STATUS_PROCESSING)) {
                        $job = new UpdateLeadBOJob();
                        $job->lead_id = $this->id;
                        $jobId = Yii::$app->queue_job->push($job);
                        // Yii::info('Lead: ' . $this->id . ', UpdateLeadBOJob: ' . $jobId, 'info\Lead:afterSave:UpdateLeadBOJob');
                    }
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

            //create or update LeadTask
            if(
                ($this->status == self::STATUS_PROCESSING && isset($changedAttributes['status'])) ||
                (isset($changedAttributes['employee_id']) && $this->status == self::STATUS_PROCESSING) ||
                (isset($changedAttributes['l_answered']) && $changedAttributes['l_answered'] != $this->l_answered)
            )
            {
                LeadTask::deleteUnnecessaryTasks($this->id);

                if($this->l_answered) {
                    $taskType = Task::CAT_ANSWERED_PROCESS;
                } else {
                    $taskType = Task::CAT_NOT_ANSWERED_PROCESS;
                }

                LeadTask::createTaskList($this->id, $this->employee_id, 1, '', $taskType);
                LeadTask::createTaskList($this->id, $this->employee_id, 2, '', $taskType);
                LeadTask::createTaskList($this->id, $this->employee_id, 3, '', $taskType);
            }

            if (!$insert) {
                foreach (['updated', 'created'] as $item) {
                    if (in_array($item, array_keys($changedAttributes))) {
                        unset($changedAttributes[$item]);
                    }
                }
                $flgUnActiveRequest = false;
                //$resetCallExpert = false;

                if (isset($changedAttributes['adults']) && $changedAttributes['adults'] != $this->adults) {
                    $flgUnActiveRequest = true;
                }
                if (isset($changedAttributes['children']) && $changedAttributes['children'] != $this->children) {
                    $flgUnActiveRequest = true;
                }
                if (isset($changedAttributes['infants']) && $changedAttributes['infants'] != $this->infants) {
                    $flgUnActiveRequest = true;
                }

                /*if (isset($changedAttributes['cabin']) && $changedAttributes['cabin'] != $this->cabin) {
                    $resetCallExpert = true;
                }
                if (isset($changedAttributes['notes_for_experts']) && $changedAttributes['notes_for_experts'] != $this->notes_for_experts) {
                    $resetCallExpert = true;
                }*/

                /*if ($resetCallExpert || $flgUnActiveRequest) {
                    Yii::$app->db->createCommand('UPDATE ' . Lead::tableName() . ' SET called_expert = :called_expert WHERE id = :id', [
                        ':called_expert' => false,
                        ':id' => $this->id
                    ])->execute();
                }*/

                if ($flgUnActiveRequest) {
                    foreach ($this->getAltQuotes() as $quote) {
                        if ($quote->status != $quote::STATUS_APPLIED) {
                            $quote->status = $quote::STATUS_DECLINED;
                            $quote->save(false);
                        }
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


    /**
     * @return string
     * @throws \Exception
     */
    public function getClientTime2(): string
    {
        $clientTime = '-';
        $offset = false;

        if ($this->offset_gmt) {
            $offset = str_replace('.', ':', $this->offset_gmt);

            if (isset($offset[0])) {
                /*if (strpos($offset, '+') === 0) {
                    $offset = str_replace('+', '-', $offset);
                } else {
                    $offset = str_replace('-', '+', $offset);
                }*/
            }

        } elseif ($this->leadFlightSegments) {

            $firstSegment = $this->leadFlightSegments[0];
            $airport = Airport::findIdentity($firstSegment->origin);
            if ($airport && $airport->dst) {
                $offset = $airport->dst;
                //$offset_gmt = $airport->dst;
            }
        }


        if ($offset) {
            /*$offset = -$offset;

            if($offset > 0) {
                $offset = '+' . $offset;
            }

            $clientTime = date('H:i', strtotime("now $offset GMT"));*/

            if(is_numeric($offset) && $offset > 0) {
                $offset = '+' . $offset;
            }

            /*try {
                $tz = new \DateTimeZone($offset);
                $dt = new \DateTime(strtotime(time()), $tz);
                $clientTime = $dt->format('H:i');
                //$clientTime = '<b title="TZ ('.$offset.') '.($this->offset_gmt ? 'by IP': 'by IATA').'"><i class="fa fa-clock-o '.($this->offset_gmt ? 'success': '').'"></i> ' . Html::encode($clientTime) . '</b>'; //<br/>(GMT: ' .$offset_gmt . ')';
                //$clientTime = $offset;
            } catch (\Exception $exception) {
                //echo $offset; exit;
                //$offset = 0;
                //$tz = new \DateTimeZone('00:00');
                //$clientTime = '-';

                //$offset = -$offset;
                if (isset($offset[0])) {
                    if (strpos($offset, '+') === 0) {
                        //$offset = str_replace('+', '-', $offset);
                    } else {
                        //$offset = str_replace('-', '+', $offset);
                    }
                }

                $clientTime = date('H:i', strtotime("now $offset GMT"));
                $clientTime = "now $offset GMT";
            }*/

            //$offset = '-2';

            $timezoneName = timezone_name_from_abbr('',intval($offset) * 60 * 60,0);

            /*$date = new \DateTime(time(), new \DateTimeZone($timezoneName));
           // $clientTime = Yii::$app->formatter->asTime() $date->format('H:i');
            $clientTime = $date->format('H:i');



            $utcTime  = new \DateTime('now', new \DateTimeZone('UTC'));


            $gmtTimezone = new \DateTimeZone($timezoneName);
            $myDateTime = new \DateTime('2019-02-18 13:28', $gmtTimezone);





            $clientTime = $utcTime->format('H:i');*/



            //-----------------------------------------------------------


            $dt = new \DateTime();
            if($timezoneName) {
                $timezone = new \DateTimeZone($timezoneName);
                $dt->setTimezone($timezone);
            }
            $clientTime =  $dt->format('H:i');


            //$clientTime = $clientTime; . ' '.$timezone->getName();  //$offset

            $clientTime = '<b title="TZ ('.$offset.') '.($this->offset_gmt ? 'by IP': 'by IATA').'"><i class="fa fa-clock-o '.($this->offset_gmt ? 'success': '').'"></i> ' . Html::encode($clientTime) . '</b>'; //<br/>(GMT: ' .$offset_gmt . ')';

            //$clientTime = $offset;

        }

        return $clientTime;
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

    public function getAppliedQuote(): ActiveQuery
    {
        return $this->hasOne(Quote::class, ['lead_id' => 'id'])->andWhere([
            'or',
            [Quote::tableName() . '.status' => Quote::STATUS_APPLIED],
            [Quote::tableName() . '.status' => null]]
        );
    }




    public function getFirstFlightSegment()
    {
        return LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['departure' => 'ASC'])->one();
    }





    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {

            if ($this->enableActiveRecordEvents) {

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

                    /*if(!$this->gid) {
                        $this->gid = md5(uniqid('', true));
                    }*/

                } else {
                    //$this->updated = date('Y-m-d H:i:s');
                }

                if(!$this->gid) {
                    $this->gid = md5(uniqid('', true));
                }

                $this->adults = (int) $this->adults;
                $this->children = (int) $this->children;
                $this->infants = (int) $this->infants;
                $this->bo_flight_id = (int) $this->bo_flight_id;
                $this->agents_processing_fee = ($this->adults + $this->children) * self::AGENT_PROCESSING_FEE_PER_PAX;

            }

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
            $source = Sources::findOne(['id' => $this->source_id]);
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

        $this->totalTips = $this->tips ? $this->tips/2 : 0;

        $processing_fee_per_pax = self::AGENT_PROCESSING_FEE_PER_PAX;

        if($this->employee_id && $this->employee) {
            $groups = $this->employee->ugsGroups;
            if($groups) {
                foreach ($groups as $group) {
                    if($group->ug_processing_fee) {
                        $processing_fee_per_pax = $group->ug_processing_fee;
                        break;
                    }
                }
                unset($groups);
            }
        }

        if($this->final_profit !== null) {
            $this->finalProfit = (float) $this->final_profit - ($processing_fee_per_pax * (int) ($this->adults + $this->children));
        } else {
            $this->finalProfit = $this->final_profit;
        }

        $this->agentProcessingFee = $processing_fee_per_pax * (int) ($this->adults + $this->children);
        $this->agents_processing_fee = ($this->agents_processing_fee)?$this->agents_processing_fee:$processing_fee_per_pax * (int) ($this->adults + $this->children);
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

        $tripType = $this->getFlightTypeName();

        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);


        $body = Yii::$app->getView()->render($view, [
            'origin' => $origin,
            'destination' => $destination,
            'quotes' => $models,
            'leadCabin' => $this->getCabinClassName(),
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

        $tripType = $this->getFlightTypeName();

        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);

        $body = Yii::$app->getView()->render($view, [
            'origin' => $origin,
            'destination' => $destination,
            'quotes' => $models,
            'leadCabin' => $this->getCabinClassName(),
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


    /**
     * @param array $quoteIds
     * @param $projectContactInfo
     * @return array
     */
    public function getEmailData2($quoteIds = [], $projectContactInfo) : array
    {
        $project = $this->project;

        $upp = null;
        if ($project) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $project->id, 'upp_user_id' => Yii::$app->user->id])->one();
            /*if ($upp) {
                $mailFrom = $upp->upp_email;
            }*/
        }


        if($quoteIds && is_array($quoteIds)) {
            foreach ($quoteIds as $qid) {
                $quoteModel = Quote::findOne($qid);
                if($quoteModel) {

                    $cabinClasses = [];
                    //$quoteItem = $quoteModel->getInfoForEmail2();
                    $quoteItem = [
                        'id' => $quoteModel->id,
                        'uid' => $quoteModel->uid,
                        'cabinClass' => $quoteModel->cabin,
                        'tripType' => $quoteModel->trip_type,

                        //'airlineCode' => $quoteModel->main_airline_code,
                        //'offerData' =>  $quoteModel->getInfoForEmail2()
                        //'shortUrl' => $quoteModel->quotePrice(),
                    ];

                    $quoteItem = array_merge($quoteItem, $quoteModel->getInfoForEmail2());

                    $content_data['quotes'][] = $quoteItem;
                }
            }
        }


        $content_data['lead'] = [
            'id'  => $this->id,
            'uid' => $this->uid
        ];

        $content_data['project'] = [
            'name'      => $project ? $project->name : '',
            'url'       => $project ? $project->link : 'https://',
            'address'   => $projectContactInfo['address'] ?? '',
            'phone'     => $projectContactInfo['phone'] ?? '',
            'email'     => $projectContactInfo['email'] ?? '',
        ];

        $content_data['agent'] = [
            'name'  => Yii::$app->user->identity->full_name,
            'username'  => Yii::$app->user->identity->username,
            'phone' => $upp && $upp->upp_phone_number ? $upp->upp_phone_number : '',
            'email' => $upp && $upp->upp_email ? $upp->upp_email : '',
        ];

        $content_data['client'] = [
            'fullName'     => $this->client ? $this->client->full_name : '',
            'firstName'    => $this->client ? $this->client->first_name : '',
            'lastName'     => $this->client ? $this->client->last_name : '',
        ];


        $arriveCity = '';
        $departCity = '';

        $arriveIATA = '';
        $departIATA = '';

        $requestSegments = [];

        if($leadSegments = $this->leadFlightSegments) {
            $firstSegment = $leadSegments[0];
            $lastSegment = end($leadSegments);

            $departIATA = $firstSegment->origin;
            $arriveIATA = $lastSegment->destination;

            $departAirport = Airport::find()->where(['iata' => $firstSegment->origin])->one();
            if($departAirport) {
                $departCity = $departAirport->city;
            } else {
                $departCity = $firstSegment->origin;
            }


            $arriveAirport = Airport::find()->where(['iata' => $firstSegment->destination])->one();
            if($arriveAirport) {
                $arriveCity = $arriveAirport->city;
            } else {
                $arriveCity = $firstSegment->destination;
            }


            /** @property string $origin
             * @property string $destination
             * @property string $departure
             * @property int $flexibility
             * @property string $flexibility_type
             * @property string $created
             * @property string $updated
             * @property string $origin_label
             * @property string $destination_label*/


            foreach($leadSegments as $segmentModel) {

                $destAirport = Airport::find()->where(['iata' => $segmentModel->destination])->one();
                $origAirport = Airport::find()->where(['iata' => $segmentModel->origin])->one();

                $requestSegments[] = [
                    'departureDate' => $segmentModel->departure,
                    'originIATA' => $segmentModel->origin,
                    'destinationIATA' => $segmentModel->destination,
                    'originCity' => $origAirport ? $origAirport->city : $segmentModel->origin,
                    'destinationCity' => $destAirport ? $destAirport->city : $segmentModel->destination,
                ];
            }

        }


        $content_data['request'] = [
            'arriveCity'    => $arriveCity,
            'departCity'    => $departCity,
            'arriveIATA'    => $arriveIATA,
            'departIATA'    => $departIATA,
            'segments'      => $requestSegments,
            'tripType'      => $this->trip_type,
            'cabinClass'    => $this->cabin,
            'paxAdt'        => (int) $this->adults,
            'paxChd'        => (int) $this->children,
            'paxInf'        => (int) $this->infants,
            'paxTotal'      => (int) $this->adults + (int) $this->children + (int) $this->infants
        ];

        return $content_data;
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
                'cabinType' => $this->getCabinClassName(),
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


    /**
     * @return array
     */
    public static function getFlightTypeList(): array
    {
        return self::TRIP_TYPE_LIST;
    }

    /**
     * @param null $flightType
     * @return string
     */
    public static function getFlightType($flightType = null): string
    {
        return self::TRIP_TYPE_LIST[$flightType] ?? '-';
    }


    /**
     * @return string
     */
    public function getFlightTypeName(): string
    {
        return self::TRIP_TYPE_LIST[$this->trip_type] ?? '-';
    }


    /**
     * @return array
     */
    public function getLeadInformationForExpert(): array
    {
        $information = [
            'trip_type' => $this->trip_type,
            'cabin' => $this->cabin,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            'notes_for_experts' => $this->notes_for_experts,
            'pref_airline' => $this->leadPreferences ? $this->leadPreferences->pref_airline : '',
            'number_stops' => $this->leadPreferences ? $this->leadPreferences->number_stops : '',
            'clients_budget' => $this->leadPreferences ? $this->leadPreferences->clients_budget : '',
            'market_price' => $this->leadPreferences ? $this->leadPreferences->market_price : '',
            'itinerary' => [],
            'agent_name' => $this->employee ? $this->employee->username : 'N/A',
            'agent_id' => $this->employee_id
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

        if($this->quotes) {
            foreach ($this->quotes as $quote) {
                $quoteArr[] = $quote->getQuoteInformationForExpert();
            }
        }


        $similarLeads = [];

        if($cloneLead = $this->clone) {
            $similarLeads[$cloneLead->id] = [
                'uid' => $cloneLead->uid,
                'gid' => $cloneLead->gid,
                'agent_username' => $cloneLead->employee ? $cloneLead->employee->username : null,
                'agent_id' => $cloneLead->employee_id,
                'created_dt' => $cloneLead->created,
                'status' => $cloneLead->status
            ];

            unset($cloneLead);
        }

        /** @var self[] $childLeads */
        $childLeads = self::find()->where(['clone_id' => $this->id])->all();

        if($childLeads) {
            foreach ($childLeads as $childLead) {
                $similarLeads[$childLead->id] = [
                    'uid' => $childLead->uid,
                    'gid' => $childLead->gid,
                    'agent_username' => $childLead->employee ? $childLead->employee->username : null,
                    'agent_id' => $childLead->employee_id,
                    'created_dt' => $childLead->created,
                    'status' => $childLead->status
                ];
            }
            unset($childLeads);
        }


        $out['call_expert'] = false;
        $out['LeadRequest'] = [
            'uid'               => $this->uid,
            'gid'               => $this->gid,
            'market_info_id'    => $this->source_id,
            'status'            => $this->status,
            'information'       => $information
        ];
        $out['similar_leads'] = $similarLeads;
        $out['LeadQuotes'] = $quoteArr;

        return $out;
    }


    /**
     * @param null $role
     * @return array
     */
    public static function getStatusList($role = null): array
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

    /**
     * @return array
     */
    public static function getProcessingStatuses(): array
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

        $taskListAll = \common\models\LeadTask::find()->with('ltTask')->select(['COUNT(*) AS field_cnt', 'lt_task_id'])->where(['lt_lead_id' => $this->id])->groupBy(['lt_task_id'])->all();
        $taskListChecked = \common\models\LeadTask::find()->with('ltTask')->select(['COUNT(*) AS field_cnt', 'lt_task_id'])->where(['lt_lead_id' => $this->id])->andWhere(['IS NOT', 'lt_completed_dt', null])->groupBy(['lt_task_id'])->all();

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
     * @param null $user_id
     * @return string
     */
    public function getCheckListInfo($user_id = null): string
    {
        $checkList = $this->leadChecklists;
        $item = [];
        if ($checkList) {
            foreach ($checkList as $task) {
                if ($user_id && $user_id !== $task->lc_user_id) {
                    continue;
                }
                $item[] = $task->lcType->lct_name;
            }
        }
        return $item ? '- ' . implode('<br>- ', $item) . '' : '';
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

        echo $command->getRawSql(); exit;

        //VarDumper::dump($command->queryAll()); exit;

        return $command->queryAll();
    }

    public function getBookedQuote()
    {
        return Quote::findOne(['lead_id' => $this->id, 'status' => Quote::STATUS_APPLIED]);
    }

    public function getBookedQuoteUid()
    {
        $query = new Query();
        $query->select(['uid'])->from('quotes')->where(['lead_id' => $this->id, 'status' => Quote::STATUS_APPLIED])->limit(1);

        return $query->createCommand()->queryScalar();
    }

    public function getFlightDetails($tag = '<br/>')
    {
        $flightSegments = LeadFlightSegment::findAll(['lead_id' => $this->id]);
        $segmentsStr = [];
        foreach ($flightSegments as $entry) {
            $segmentsStr[] = $entry['departure'] . ' ' . $entry['origin'] . '-' . $entry['destination'];
        }

        return implode($tag, $segmentsStr);
    }

    public function getDeparture()
    {
        $flightSegment = LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['departure' => SORT_ASC])->one();

        return $flightSegment ? $flightSegment['departure'] : null;
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

        $query->with(['mainAirline', 'quoteTrips.quoteSegments.marketingAirline', 'quoteTrips.quoteSegments.quoteSegmentBaggages',
            'quoteTrips.quoteSegments.quoteSegmentBaggageCharges', 'quoteTrips.quoteSegments.quoteSegmentStops']);

        return $dataProvider;
    }


    /**
     * @return string
     */
    public function generateLeadKey(): string
    {
        $leadFlights = $this->leadFlightSegments;
        $key = $this->cabin;
        foreach ($leadFlights as $flEntry){
            $key .= $flEntry->origin.$flEntry->destination.strtotime($flEntry->departure).$flEntry->flexibility_type.$flEntry->flexibility;
        }
        $key .= '_'.$this->adults.'_'.$this->children.'_'.$this->infants;
        return $key;
    }

    /**
     * @param int $type_id
     * @return int|string
     */
    public function getCountCalls(int $type_id = 0)
    {
        if($type_id === 0) {
            $count = Call::find()->where(['c_lead_id' => $this->id, 'c_is_deleted' => false])->count();
        } else {
            $count = Call::find()->where(['c_lead_id' => $this->id, 'c_is_deleted' => false, 'c_call_type_id' => $type_id])->count();
        }
        return $count;
    }


    /**
     * @param int $type_id
     * @return int|string
     */
    public function getCountSms(int $type_id = 0)
    {
        if($type_id === 0) {
            $count = Sms::find()->where(['s_lead_id' => $this->id, 's_is_deleted' => false])->count();
        } else {
            $count = Sms::find()->where(['s_lead_id' => $this->id, 's_type_id' => $type_id, 's_is_deleted' => false])->count();
        }
        return $count;
    }


    /**
     * @param int $type_id
     * @return int|string
     */
    public function getCountEmails(int $type_id = 0)
    {
        if($type_id === 0) {
            $count = Email::find()->where(['e_lead_id' => $this->id, 'e_is_deleted' => false])->count();
        } else {
            $count = Email::find()->where(['e_lead_id' => $this->id, 'e_type_id' => $type_id, 'e_is_deleted' => false])->count();
        }
        return $count;
    }


    /**
     * @param int|null $user_id
     * @return ActiveQuery
     */
    public static function getPendingQuery(int $user_id = null): ActiveQuery
    {
        $query = self::find();
        $query->select(['*', 'l_client_time' => new Expression("TIME( CONVERT_TZ(NOW(), '+00:00', offset_gmt) )")]);

        $query->andWhere(['status' => self::STATUS_PENDING, 'l_call_status_id' => [self::CALL_STATUS_READY, self::CALL_STATUS_NONE]]);
        $query->andWhere(['OR', ['IS', 'l_pending_delay_dt', null], ['<=', 'l_pending_delay_dt', date('Y-m-d H:i:s')]]);
        $query->andWhere(['OR', ['BETWEEN', new Expression('TIME(CONVERT_TZ(NOW(), \'+00:00\', offset_gmt))'), '09:00', '21:00'], ['>=', 'created', date('Y-m-d H:i:s', strtotime('-'.self::PENDING_ALLOW_CALL_TIME_MINUTES.' min'))]]);
        $query->andWhere(['OR', ['employee_id' => null], ['employee_id' => $user_id]]);

        if($user_id) {
            $subQuery = UserProjectParams::find()->select(['upp_project_id'])->where(['upp_user_id' => $user_id])->andWhere(['AND', ['IS NOT', 'upp_tw_phone_number', null], ['<>', 'upp_tw_phone_number', '']]);
            $query->andWhere(['IN', 'project_id', $subQuery]);
        }
        //$query->andWhere(['request_ip' => ['217.26.162.22']]);

        $query->orderBy(['id' => SORT_DESC]);

        return $query;
    }

    /**
     * @return int
     */
    public function getDelayPendingTime(): int
    {
        $min = 0;

        if($this->created) {
            $diffSeconds = time() - strtotime($this->created);
        } else {
            $diffSeconds = 0;
        }

        $hour = 60;

        $diffMin = ceil($diffSeconds / $hour);

        if($diffMin < $hour) {
            $min = 10;
        } elseif($diffMin < ($hour * 4)) {
            $min = 30;
        } elseif($diffMin < ($hour * 72)) {
            $min = 180;
        } elseif($diffMin < ($hour * 192)) {
            $min = 180;
        } /*else {
            $min = 120;
        }*/

        // 120 h - 192 h

        return $min;
    }




}
