<?php

namespace common\models;

use common\components\ga\GaHelper;
use common\components\jobs\UpdateLeadBOJob;
use common\components\purifier\Purifier;
use common\models\local\LeadAdditionalInformation;
use common\models\query\LeadQuery;
use common\models\query\SourcesQuery;
use DateTime;
use frontend\helpers\RedisHelper;
use frontend\helpers\JsonHelper;
use frontend\widgets\notification\NotificationMessage;
use kivork\search\core\urlsig\UrlSignature;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\queue\LeadBusinessExtraQueueAbacDto;
use modules\lead\src\abac\queue\LeadBusinessExtraQueueAbacObject;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\services\ObjectTaskService;
use modules\offer\src\entities\offer\Offer;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\product\Product;
use src\auth\Auth;
use src\behaviors\metric\MetricLeadCounterBehavior;
use src\entities\EventTrait;
use src\events\lead\LeadBookedEvent;
use src\events\lead\LeadBusinessExtraQueueEvent;
use src\events\lead\LeadCallExpertChangedEvent;
use src\events\lead\LeadCallExpertRequestEvent;
use src\events\lead\LeadCallStatusChangeEvent;
use src\events\lead\LeadCloseEvent;
use src\events\lead\LeadCountPassengersChangedEvent;
use src\events\lead\LeadCreatedBookFailedEvent;
use src\events\lead\LeadCreatedByApiBOEvent;
use src\events\lead\LeadCreatedByApiEvent;
use src\events\lead\LeadCreatedByIncomingCallEvent;
use src\events\lead\LeadCreatedByIncomingEmailEvent;
use src\events\lead\LeadCreatedByIncomingSmsEvent;
use src\events\lead\LeadCreatedClientChatEvent;
use src\events\lead\LeadCreatedCloneEvent;
use src\events\lead\LeadCreatedEvent;
use src\events\lead\LeadCreatedManuallyEvent;
use src\events\lead\LeadCreatedNewEvent;
use src\events\lead\LeadDuplicateDetectedEvent;
use src\events\lead\LeadExtraQueueEvent;
use src\events\lead\LeadFollowUpEvent;
use src\events\lead\LeadNewEvent;
use src\events\lead\LeadOwnerChangedEvent;
use src\events\lead\LeadOwnerFreedEvent;
use src\events\lead\LeadPendingEvent;
use src\events\lead\LeadPoorProcessingEvent;
use src\events\lead\LeadProcessingEvent;
use src\events\lead\LeadRejectEvent;
use src\events\lead\LeadSnoozeEvent;
use src\events\lead\LeadSoldEvent;
use src\events\lead\LeadStatusChangedEvent;
use src\events\lead\LeadTaskEvent;
use src\events\lead\LeadTrashEvent;
use src\formatters\client\ClientTimeFormatter;
use src\helpers\app\AppHelper;
use src\helpers\lead\LeadHelper;
use src\helpers\lead\LeadUrlHelper;
use src\helpers\setting\SettingHelper;
use src\interfaces\Objectable;
use src\model\airportLang\service\AirportLangService;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\client\helpers\ClientFormatter;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\lead\useCases\lead\api\create\LeadCreateForm;
use src\model\lead\useCases\lead\import\LeadImportForm;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadData\entity\LeadData;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\leadUserRating\entity\LeadUserRating;
use src\repositories\client\ClientPhoneRepository;
use src\services\lead\calculator\LeadTripTypeCalculator;
use src\services\lead\calculator\SegmentDTO;
use src\services\lead\qcall\Config;
use src\services\lead\qcall\FindPhoneParams;
use src\services\lead\qcall\FindWeightParams;
use src\services\lead\qcall\QCallService;
use Yii;
use yii\base\InvalidArgumentException;
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
use src\repositories\email\EmailRepositoryFactory;
use src\entities\email\helpers\EmailType;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
 * @property int|null $client_id
 * @property int|null $employee_id
 * @property int|null $status
 * @property string|null $uid
 * @property int|null $project_id
 * @property int|null $source_id
 * @property string|null $trip_type
 * @property string|null $cabin
 * @property int|null $adults
 * @property int|null $children
 * @property int|null $infants
 * @property string|null $notes_for_experts
 * @property string|null $created
 * @property string|null $updated
 * @property string|null $request_ip
 * @property string|null $request_ip_detail
 * @property string|null $offset_gmt
 * @property string|null $snooze_for
 * @property int|null $rating
 * @property int|null $called_expert
 * @property string|null $discount_id
 * @property int|null $bo_flight_id
 * @property string|null $additional_information
 * @property int|null $l_answered
 * @property int|null $clone_id
 * @property string|null $description
 * @property float|null $final_profit
 * @property float|null $tips
 * @property string|null $gid
 * @property float|null $agents_processing_fee
 * @property int|null $l_call_status_id
 * @property string|null $l_pending_delay_dt
 * @property string|null $l_client_first_name
 * @property string|null $l_client_last_name
 * @property string|null $l_client_phone
 * @property string|null $l_client_email
 * @property string|null $l_client_lang
 * @property string|null $l_client_ua
 * @property string|null $l_request_hash
 * @property int|null $l_duplicate_lead_id
 * @property float|null $l_init_price
 * @property string|null $l_last_action_dt
 * @property int|null $l_dep_id
 * @property int|null $l_delayed_charge
 * @property int|null $l_type_create
 * @property int $l_is_test
 * @property string|null $hybrid_uid
 * @property int|null $l_visitor_log_id
 * @property string|null $l_status_dt
 * @property string|null $l_expiration_dt
 * @property int|null $l_type
 *
 * @property float $finalProfit
 * @property int $quotesCount
 * @property int $leadFlightSegmentsCount
 * @property int $quotesExpertCount
 * @property float $agentsProcessingFee
 * @property string $l_client_time
 * @property boolean $enableActiveRecordEvents
 * @property $totalTips
 * @property $processingFeePerPax
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
 * @property Offer[] $offers
 * @property Order[] $orders
 * @property Product[] $products
 * @property LeadFlightSegment[] $leadFlightSegments
 * @property LeadFlow[] $leadFlows
 * @property LeadPreferences $leadPreferences
 * @property LeadQcall $leadQcall
 * @property Client $client
 * @property Employee $employee
 * @property Lead $lDuplicateLead
 * @property Lead[] $leads0
 * @property Department $lDep
 * @property Sources $source
 * @property Project $project
 * @property LeadAdditionalInformation[] $additionalInformationForm
 * @property LeadAdditionalInformation[] $oldAdditionalInformationForm
 * @property LeadUserRating[] $leadUserRatings
 * @property LeadUserRating $leadUserRatingByUser
 * @property Lead $clone
 * @property ProfitSplit[] $profitSplits
 * @property TipsSplit[] $tipsSplits
 * @property UserConnection[] $userConnections
 * @property LeadData[] $leadData
 * @property LeadUserConversion[] $leadUserConversion
 *
 * @property LeadFlow $lastLeadFlow
 *
 * @property $remainingDays
 * @property $grade
 * @property $inCalls
 * @property $inCallsDuration
 * @property $outCalls
 * @property $outCallsDuration
 * @property $smsOffers
 * @property $emailOffers
 * @property int $delayPendingTime
 * @property null|DateTime $clientTime2
 * @property mixed $sumPercentProfitSplit
 * @property ActiveQuery $leadTasks
 * @property null|string $tripType
 * @property mixed $firstFlightSegment
 * @property bool $answered
 * @property null|DateTime $clientTime2Old
 * @property mixed $bookedQuoteUid
 * @property int $owner
 * @property mixed $allTipsSplits
 * @property string $taskInfo
 * @property null|string $lastActivityByNote
 * @property int $callStatus
 * @property mixed $quoteSendInfo
 * @property null|string $additionalInformationFormFirstElementPnr
 * @property string $statusLabelClass
 * @property array $paxTypes
 * @property mixed $bookedQuote
 * @property null $departure
 * @property ActiveQuery $tsUsers
 * @property string $requestHash
 * @property \common\models\local\LeadAdditionalInformation $additionalInformationFormFirstElement
 * @property \common\models\local\LeadAdditionalInformation $oldAdditionalInformationFormFirstElement
 * @property null|Quote $appliedAlternativeQuotes
 * @property mixed $flowTransition
 * @property mixed $sumPercentTipsSplit
 * @property null|Quote $appliedQuote
 * @property ActiveQuery $psUsers
 * @property mixed $allProfitSplits
 * @property null|string $lastReasonFromLeadFlow
 * @property array|Quote[] $altQuotes
 * @property string|mixed $cabinClassName
 * @property array $leadInformationForExpert
 * @property ActiveQuery $leadFlowSold
 * @property string $communicationInfo
 * @property int $countOutCallsLastFlow
 * @property string $flightTypeName
 * @property mixed $sentCount
 * @property bool $calledExpert
 * @property $quoteType
 * @property Language $language
 * @property LeadPoorProcessing $minLpp
 *
 */
class Lead extends ActiveRecord implements Objectable
{
    use EventTrait;

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
    public const STATUS_BOOK_FAILED = 14;
    public const STATUS_ALTERNATIVE = 15;
    public const STATUS_NEW         = 16;
    public const STATUS_EXTRA_QUEUE = 17;
    public const STATUS_CLOSED      = 18;
    public const STATUS_BUSINESS_EXTRA_QUEUE = 19;

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
        self::STATUS_BOOK_FAILED    => 'Book failed',
        self::STATUS_ALTERNATIVE    => 'Alternative',
        self::STATUS_NEW            => 'New',
        self::STATUS_EXTRA_QUEUE    => 'Extra queue',
        self::STATUS_CLOSED         => 'Closed',
        self::STATUS_BUSINESS_EXTRA_QUEUE => 'Business extra queue',
    ];

    public const TRAVEL_DATE_PASSED_STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_FOLLOW_UP,
        self::STATUS_NEW,
    ];

    public const STATUS_MULTIPLE_UPDATE_LIST = [
        self::STATUS_FOLLOW_UP      => self::STATUS_LIST[self::STATUS_FOLLOW_UP],
        self::STATUS_ON_HOLD        => self::STATUS_LIST[self::STATUS_ON_HOLD],
        self::STATUS_PROCESSING     => self::STATUS_LIST[self::STATUS_PROCESSING],
        self::STATUS_TRASH          => self::STATUS_LIST[self::STATUS_TRASH],
        self::STATUS_BOOKED         => self::STATUS_LIST[self::STATUS_BOOKED],
        self::STATUS_SNOOZE         => self::STATUS_LIST[self::STATUS_SNOOZE],
        self::STATUS_CLOSED         => self::STATUS_LIST[self::STATUS_CLOSED],
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
        self::STATUS_CLOSED         => 'll-close',
        self::STATUS_REJECT         => 'label-default',
        self::STATUS_NEW            => 'label-default',
        self::STATUS_BOOK_FAILED    => 'label-default',
        self::STATUS_ALTERNATIVE    => 'label-default',
        self::STATUS_EXTRA_QUEUE    => 'label-default',
        self::STATUS_BUSINESS_EXTRA_QUEUE => 'label-default',
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
    public const CALL_STATUS_PREPARE    = 6;
    public const CALL_STATUS_BUGGED    = 7;

    public const CALL_STATUS_LIST = [
        self::CALL_STATUS_NONE      => 'None',
        self::CALL_STATUS_READY     => 'Ready',
        self::CALL_STATUS_PROCESS   => 'Process',
        self::CALL_STATUS_CANCEL    => 'Cancel',
        self::CALL_STATUS_DONE      => 'Done',
        self::CALL_STATUS_QUEUE     => 'Queue',
        self::CALL_STATUS_PREPARE   => 'Prepare',
        self::CALL_STATUS_BUGGED   => 'Bugged',
    ];

    public const TYPE_CREATE_MANUALLY = 1;
    public const TYPE_CREATE_INCOMING_CALL = 2;
    public const TYPE_CREATE_API = 3;
    public const TYPE_CREATE_INCOMING_SMS = 4;
    public const TYPE_CREATE_INCOMING_EMAIL = 5;
    public const TYPE_CREATE_CLONE = 6;
    public const TYPE_CREATE_IMPORT = 7;
    public const TYPE_CREATE_CLIENT_CHAT = 8;
    public const TYPE_CREATE_MANUALLY_FROM_CALL = 9;

    public const TYPE_CREATE_LIST = [
        self::TYPE_CREATE_MANUALLY => 'Manually',
        self::TYPE_CREATE_INCOMING_CALL => 'Incoming call',
        self::TYPE_CREATE_API => 'Api',
        self::TYPE_CREATE_INCOMING_SMS => 'Incoming sms',
        self::TYPE_CREATE_INCOMING_EMAIL => 'Incoming email',
        self::TYPE_CREATE_CLONE => 'Clone',
        self::TYPE_CREATE_IMPORT => 'Import',
        self::TYPE_CREATE_CLIENT_CHAT => 'Client Chat',
        self::TYPE_CREATE_MANUALLY_FROM_CALL => 'Manually from call',
    ];

    public const TYPE_BASIC = 1;
    public const TYPE_ALTERNATIVE = 2;
    public const TYPE_FAILED_BOOK = 3;

    public const TYPE_LIST = [
        //self::TYPE_BASIC => 'Basic',
        self::TYPE_ALTERNATIVE => 'Alternative',
        self::TYPE_FAILED_BOOK => 'Failed Book'
    ];

    private const PROCESSED_VTF = [
        0 => 'Pending',
        1 => 'Verified'
    ];

    private const PROCESSED_TKT = [
        0 => 'Pending',
        1 => 'Issued'
    ];

    private const PROCESSED_EXP = [
        0 => 'Pending',
        1 => 'Checked'
    ];

    public const SCENARIO_API = 'scenario_api';
    public const SCENARIO_MULTIPLE_UPDATE = 'scenario_multiple_update';

    public $status_description;
    public $totalProfit;
    public $splitProfitPercentSum = 0;
    public $splitTipsPercentSum = 0;

    public $l_client_time;

    public $enableActiveRecordEvents = true;

    public $remainingDays;

    public $grade;
    public $inCalls;
    public $inCallsDuration;
    public $outCalls;
    public $outCallsDuration;
    public $smsOffers;
    public $emailOffers;
    public $quoteType;

    private $oldAdditionalInformation;
    private ?array $objectTaskOrderByStatus = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'leads';
    }

    /**
     * @return bool
     */
    public function isManuallyCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_MANUALLY;
    }

    /**
     * @return bool
     */
    public function isIncomingCallCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_INCOMING_CALL;
    }

    /**
     * @return bool
     */
    public function isApiCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_API;
    }

    /**
     * @return bool
     */
    public function isIncomingSmsCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_INCOMING_SMS;
    }

    /**
     * @return bool
     */
    public function isIncomingEmailCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_INCOMING_EMAIL;
    }

    /**
     * @return bool
     */
    public function isCloneCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_CLONE;
    }

    public function isClientChatCreated(): bool
    {
        return $this->l_type_create === self::TYPE_CREATE_CLIENT_CHAT;
    }

//    public function init()
//    {
//        parent::init();
//        $this->additionalInformationForm = [new LeadAdditionalInformation()];
//    }

    private $additionalInformationForm;
    private $oldAdditionalInformationForm;

    /**
     * @return LeadAdditionalInformation[]
     */
    public function getAdditionalInformationForm(): array
    {
        if ($this->additionalInformationForm !== null) {
            return $this->additionalInformationForm;
        }

        if (!empty($this->additional_information)) {
            $this->additionalInformationForm = self::getLeadAdditionalInfo($this->additional_information);
        } else {
            $this->additionalInformationForm = [new LeadAdditionalInformation()];
        }

        return $this->additionalInformationForm;
    }

    /**
     * @return LeadAdditionalInformation[]
     */
    public function getOldAdditionalInformationForm(): array
    {
        if ($this->oldAdditionalInformationForm !== null) {
            return $this->oldAdditionalInformationForm;
        }

        if (!empty($this->oldAdditionalInformation)) {
            $this->oldAdditionalInformationForm = self::getLeadAdditionalInfo($this->oldAdditionalInformation);
        } else {
            $this->oldAdditionalInformationForm = [new LeadAdditionalInformation()];
        }

        return $this->oldAdditionalInformationForm;
    }

    /**
     * @return LeadAdditionalInformation
     */
    public function getAdditionalInformationFormFirstElement(): LeadAdditionalInformation
    {
        return $this->getAdditionalInformationForm()[0];
    }

    /**
     * @return LeadAdditionalInformation
     */
    public function getOldAdditionalInformationFormFirstElement(): LeadAdditionalInformation
    {
        return $this->getOldAdditionalInformationForm()[0];
    }

    /**
     * @return array|null
     */
    public function getAdditionalInformationMultiplePnr(): array
    {
        $data = [];
        foreach ($this->getAdditionalInformationForm() as $key => $obj) {
            array_push($data, $obj->pnr);
        }
        return $data;
    }

    /**
     * @param string|null $pnr
     */
    public function setAdditionalInformationFormFirstElementPnr(?string $pnr): void
    {
        $additionalInfo = $this->getAdditionalInformationFormFirstElement();
        $additionalInfo->pnr = $pnr;
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

            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'rating', 'bo_flight_id', 'clone_id', 'l_call_status_id', 'l_duplicate_lead_id', 'l_dep_id', 'l_is_test'], 'integer'],
            [['adults', 'children', 'infants'], 'integer', 'max' => 9],

            [['notes_for_experts', 'request_ip_detail', 'l_client_ua'], 'string'],

            [['created', 'updated', 'snooze_for', 'called_expert', 'additional_information',
              'l_pending_delay_dt', 'l_last_action_dt', 'l_status_dt'], 'safe'],

            [['final_profit', 'tips', 'agents_processing_fee', 'l_init_price'], 'number'],
            [['uid', 'request_ip', 'offset_gmt', 'discount_id', 'description'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['hybrid_uid'], 'string', 'max' => 15],
            [['cabin'], 'string', 'max' => 1],
            [['gid', 'l_request_hash'], 'string', 'max' => 32],
            [['l_client_first_name', 'l_client_last_name'], 'string', 'max' => 50],
            [['l_client_phone'], 'string', 'max' => 20],
            [['l_client_email'], 'string', 'max' => 160],
            [['gid'], 'unique'],
            [['l_answered'], 'boolean'],
            [['status_description'], 'string'],

            [['clone_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['clone_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['l_duplicate_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['l_duplicate_lead_id' => 'id']],
            [['l_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['l_dep_id' => 'dep_id']],

            ['l_delayed_charge', 'boolean'],
            ['l_delayed_charge', 'default', 'value' => false],

            ['l_type_create', 'in', 'range' => array_keys(self::TYPE_CREATE_LIST)],

            ['l_visitor_log_id', 'integer'],
            ['l_visitor_log_id', 'exist', 'skipOnError' => true, 'targetClass' => VisitorLog::class, 'targetAttribute' => ['l_visitor_log_id' => 'vl_id']],

            [['l_client_lang'], 'string', 'max' => 5],
            ['l_client_lang', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['l_client_lang' => 'language_id']],

            [['l_expiration_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['l_type', 'integer']
        ];
    }

    /**
     * @return static
     */
    private static function create(): self
    {
        $lead = new static();
        $lead->uid = self::generateUid();
        $lead->gid = self::generateGid();
        $lead->recordEvent(new LeadCreatedEvent($lead));
        return $lead;
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
     * @param $delayedCharge
     * @param $typeCreate
     * @return Lead
     */
    public static function createManually(
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
        $depId,
        $delayedCharge,
        $typeCreate
    ): self {
        $lead = self::create();
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
        $lead->l_client_phone = $clientPhone;
        $lead->l_client_email = $clientEmail;
        $lead->l_dep_id = $depId;
        $lead->l_delayed_charge = $delayedCharge;
        $lead->status = null;
        $lead->l_type_create = $typeCreate;
        $lead->recordEvent(new LeadCreatedManuallyEvent($lead));
        return $lead;
    }

    /**
     * @param string|null $description
     * @return Lead
     */
    public function createClone(?string $description): self
    {
        $clone                         = self::create();
        $clone->attributes             = $this->attributes;
        $clone->description            = $description;
        $clone->notes_for_experts      = null;
        $clone->rating                 = 0;
        $clone->additional_information = null;
        $clone->l_answered             = 0;
        $clone->snooze_for             = null;
        $clone->called_expert          = false;
        $clone->created                = null;
        $clone->updated                = null;
        $clone->tips                   = 0;
        $clone->uid                    = self::generateUid();
        $clone->gid                    = self::generateGid();
        $clone->status                 = null;
        $clone->clone_id               = $this->id;
        $clone->employee_id            = null;
        $clone->hybrid_uid            = null;
        $clone->l_type_create          = self::TYPE_CREATE_CLONE;
        $clone->bo_flight_id           = 0;
        $clone->final_profit           = null;
        $clone->l_delayed_charge       = 0;

        $clone->recordEvent(new LeadCreatedCloneEvent($clone));

        return $clone;
    }

    /**
     * @param string $clientEmail
     * @param int $clientId
     * @param int|null $projectId
     * @param int|null $sourceId
     * @param int $departmentId
     * @return Lead
     */
    public static function createByIncomingEmail(
        string $clientEmail,
        int $clientId,
        ?int $projectId,
        ?int $sourceId,
        int $departmentId
    ): self {
        $lead = self::create();
        $lead->l_client_email = $clientEmail;
        $lead->client_id = $clientId;
        $lead->project_id = $projectId;
        $lead->source_id = $sourceId;
        $lead->l_dep_id = $departmentId;
        $lead->status = self::STATUS_PENDING;
        $lead->l_type_create = self::TYPE_CREATE_INCOMING_EMAIL;
        $lead->recordEvent(new LeadCreatedByIncomingEmailEvent($lead));
        return $lead;
    }

    public static function createByIncomingSms(
        string $clientPhone,
        int $clientId,
        ?int $projectId,
        ?int $sourceId,
        int $departmentId
    ): self {
        $lead = self::create();
        $lead->l_client_phone = $clientPhone;
        $lead->client_id = $clientId;
        $lead->project_id = $projectId;
        $lead->source_id = $sourceId;
        $lead->l_dep_id = $departmentId;
        $lead->status = self::STATUS_PENDING;
        $lead->l_type_create = self::TYPE_CREATE_INCOMING_SMS;
        $lead->recordEvent(new LeadCreatedByIncomingSmsEvent($lead));
        return $lead;
    }

    /**
     * @param $phoneNumber
     * @param $clientId
     * @param $projectId
     * @param $sourceId
     * @param $departmentId
     * @param $gmt
     * @return Lead
     */
    public static function createByIncomingCall(
        $phoneNumber,
        $clientId,
        $projectId,
        $sourceId,
        $departmentId,
        $gmt
    ): self {
        $lead = self::create();
        $lead->l_client_phone = $phoneNumber;
        $lead->client_id = $clientId;
        $lead->project_id = $projectId;
        $lead->source_id = $sourceId;
        $lead->offset_gmt = $gmt;
        $lead->l_dep_id = $departmentId;
        $lead->status = self::STATUS_PENDING;
        $lead->l_type_create = self::TYPE_CREATE_INCOMING_CALL;
        $lead->l_call_status_id = self::CALL_STATUS_QUEUE;
        $lead->recordEvent(new LeadCreatedByIncomingCallEvent($lead));
        return $lead;
    }

    /**
     * @return static
     */
    public static function createByApi(): self
    {
        $lead = self::create();
        $lead->l_dep_id = Department::DEPARTMENT_SALES;
        $lead->scenario = self::SCENARIO_API;
        $lead->l_type_create = self::TYPE_CREATE_API;
        return $lead;
    }

    public function eventLeadCreatedByApiEvent(): void
    {
        $this->recordEvent(new LeadCreatedByApiEvent($this, $this->status));
    }

    public static function createByApiBO(LeadCreateForm $form, Client $client): self
    {
        $lead = self::create();
        $lead->client_id = $client->id;
        $lead->l_client_first_name = $client->first_name;
        $lead->l_client_last_name = $client->last_name;
        $lead->l_client_phone = $form->clientForm->phone;
        $lead->l_client_email = $form->clientForm->email;
        $lead->l_client_ua = $form->user_agent;
        $lead->project_id = $form->project_id;
        $lead->source_id = $form->source_id;
        $lead->cabin = $form->cabin;
        $lead->adults = $form->adults;
        $lead->children = $form->children;
        $lead->infants = $form->infants;
        $lead->request_ip = $form->request_ip;
        $lead->discount_id = $form->discount_id;
        $lead->uid = $form->uid ?: $lead->uid;
        $lead->hybrid_uid = $form->uid;
        $lead->status = $form->status;
        $lead->bo_flight_id = $form->flight_id;
        $lead->l_dep_id = $form->department_id;
        $lead->l_type_create = self::TYPE_CREATE_API;
        $lead->l_client_lang = $form->user_language;
        $lead->l_expiration_dt = $form->expire_at;
        $lead->l_type = $form->type;
        return $lead;
    }

    public function eventLeadCreatedByApiBOEvent(): void
    {
        $this->recordEvent(new LeadCreatedByApiBOEvent($this, $this->status));
    }

    public static function createNew(LeadImportForm $form, Client $client, ?int $creatorId): self
    {
        $lead = self::create();
        $lead->status = self::STATUS_NEW;
        $lead->l_dep_id = Department::DEPARTMENT_SALES;
        $lead->l_type_create = self::TYPE_CREATE_IMPORT;
        $lead->project_id = $form->project_id;
        $lead->source_id = $form->source_id;
        $lead->client_id = $client->id;
        $lead->l_client_first_name = $client->first_name;
        $lead->l_client_last_name = $client->last_name;
        $lead->l_client_email = $form->client->email;
        $lead->l_client_phone = $form->client->phone;
        $lead->rating = $form->rating;
        $lead->notes_for_experts = $form->notes;
        $lead->recordEvent(new LeadCreatedNewEvent($lead, $creatorId));
        return $lead;
    }

    public static function createByClientChat(
        $clientId,
        $clientFirstName,
        $clientLastName,
        $requestIp,
        $sourceId,
        $projectId,
        $depId,
        ?int $creatorId,
        ?int $visitorLogId,
        ?string $ip,
        ?string $gmtOffset
    ): self {
        $lead = self::create();
        $lead->client_id = $clientId;
        $lead->l_client_first_name = $clientFirstName;
        $lead->l_client_last_name = $clientLastName;
        $lead->request_ip = $requestIp;
        $lead->source_id = $sourceId;
        $lead->project_id = $projectId;
        $lead->l_dep_id = $depId;
        $lead->status = null;
        $lead->l_type_create = self::TYPE_CREATE_CLIENT_CHAT;
        $lead->l_visitor_log_id = $visitorLogId;
        $lead->request_ip = $ip;
        $lead->offset_gmt = $gmtOffset;
        $lead->recordEvent(new LeadCreatedClientChatEvent($lead, $creatorId));
        return $lead;
    }

    public static function createBookFailed($projectId, $department, $clientId): self
    {
        $lead = self::create();
        $lead->project_id = $projectId;
        $lead->l_dep_id = $department;
        $lead->status = self::STATUS_BOOK_FAILED;
        $lead->client_id = $clientId;
        $lead->recordEvent(new LeadCreatedBookFailedEvent($lead, $lead->status));
        return $lead;
    }

    /**
     * @param array $segments
     * @return bool
     */
    public function equalsSegments(array $segments): bool
    {
        $originSegments = [];
        foreach ($this->leadFlightSegments as $segment) {
            $originSegments[] = [
                'origin' => $segment->origin,
                'destination' => $segment->destination,
                'departure' => $segment->departure
            ];
        }
        return $segments === $originSegments;
    }

    public function originalQuoteExist(int $excludeQuoteId = null): bool
    {
//        return Quote::find()->originalExist($this->id);
        foreach ($this->quotes as $quote) {
            if ($quote->isOriginal()) {
                if ($excludeQuoteId) {
                    if ($quote->id !== $excludeQuoteId) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAvailableForMultiUpdate(): bool
    {
        return $this->isProcessing() || $this->isFollowUp() || $this->isOnHold() || $this->isTrash() || $this->isSnooze();
    }

    /**
     * @return string
     */
    public static function generateUid(): string
    {
        return uniqid();
    }

    /**
     * @return string
     */
    public static function generateGid(): string
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

    public function answered()
    {
        $this->setAnswered(true);
    }

    public function notAnswered()
    {
        $this->setAnswered(false);
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

    public function hasOwner(): bool
    {
        return $this->employee_id ? true : false;
    }

    /**
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return $this->employee_id === $userId;
    }

    /**
     * @param int $ownerId
     */
    public function setOwner(int $ownerId): void
    {
        if ($this->isOwner($ownerId)) {
            throw new \DomainException('This user already is owner.');
        }

        $this->recordEvent(new LeadOwnerChangedEvent($this, $this->employee_id, $ownerId));

        $this->employee_id = $ownerId;
    }

    private function freedOwner(): void
    {
        if ($this->isFreedOwner()) {
            throw new \DomainException('This Lead is already freed owner.');
        }

        $this->recordEvent(new LeadOwnerFreedEvent($this, $this->employee_id));

        $this->employee_id = null;
    }

    /**
     * @return bool
     */
    private function isFreedOwner(): bool
    {
        return $this->employee_id === null;
    }

    /**
     * @param int $status
     */
    private function setStatus(int $status): void
    {
        if (!array_key_exists($status, self::STATUS_LIST)) {
            throw new InvalidArgumentException('Invalid Status');
        }

        if ($this->status === $status) {
            throw new \DomainException('Lead is already ' . self::STATUS_LIST[$status]);
        }

        $this->recordEvent(new LeadStatusChangedEvent($this, $this->status, $status, $this->employee_id));

        if ($this->isCalledExpert() && in_array($status, [self::STATUS_TRASH, self::STATUS_FOLLOW_UP, self::STATUS_SNOOZE, self::STATUS_PROCESSING], true)) {
            $this->recordEvent(new LeadCallExpertRequestEvent($this));
        }

        $this->status = $status;
        $this->l_status_dt = date('Y-m-d H:i:s');

        if (LeadHelper::checkCallExpertNeededChange($this)) {
            $this->recordEvent(new LeadCallExpertChangedEvent($this->id));
        }
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

    public function recalculateTripType()
    {
        $segmentsDTO = [];

        foreach ($this->leadFlightSegments as $segment) {
            $segmentsDTO[] = new SegmentDTO($segment->origin, $segment->destination);
        }

        $type = LeadTripTypeCalculator::calculate(...$segmentsDTO);

        $this->setTripType($type);
    }

    /**
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function sold(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_SOLD);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isSold()) {
            throw new \DomainException('Lead is already Sold without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isSold()) {
            throw new \DomainException('Lead is already Sold with this owner.');
        }

        $this->recordEvent(
            new LeadSoldEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isSold()) {
            $this->setStatus(self::STATUS_SOLD);
        }
    }

    /**
     * @return bool
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    /**
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function booked(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_BOOKED);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isBooked()) {
            throw new \DomainException('Lead is already Booked without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isBooked()) {
            throw new \DomainException('Lead is already Booked with this owner.');
        }

        $this->recordEvent(
            new LeadBookedEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isBooked()) {
            $this->setStatus(self::STATUS_BOOKED);
        }
    }

    /**
     * @return bool
     */
    public function isBooked(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    /**
     * @param string|null $snoozeFor
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function snooze(?string $snoozeFor, ?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_SNOOZE);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isSnooze()) {
            throw new \DomainException('Lead is already Snooze without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isSnooze()) {
            throw new \DomainException('Lead is already Snooze with this owner.');
        }

        if ($snoozeFor) {
            $snoozeFor = date('Y-m-d H:i:s', strtotime($snoozeFor));
        } else {
            $snoozeFor = null;
        }
        $this->snooze_for = $snoozeFor;

        $this->recordEvent(
            new LeadSnoozeEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason,
                $snoozeFor
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isSnooze()) {
            $this->setStatus(self::STATUS_SNOOZE);
        }
    }

    /**
     * @return bool
     */
    public function isSnooze(): bool
    {
        return $this->status === self::STATUS_SNOOZE;
    }

    public function isSnoozeExpired(int $timeNow): bool
    {
        if ($this->snooze_for) {
            $snoozeTimeNow = strtotime($this->snooze_for);
            return $timeNow > $snoozeTimeNow;
        }
        return true;
    }

    /**
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function followUp(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_FOLLOW_UP);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isFollowUp()) {
            throw new \DomainException('Lead is already Follow Up without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isFollowUp()) {
            throw new \DomainException('Lead is already Follow Up with this owner.');
        }

        $this->recordEvent(
            new LeadFollowUpEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isFollowUp()) {
            $this->setStatus(self::STATUS_FOLLOW_UP);
        }
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
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function processing(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_PROCESSING);
        $oldStatus = $this->status;

        if ($newOwnerId === null && !$this->hasOwner() && $this->isProcessing()) {
            throw new \DomainException('Lead is already Processing without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isProcessing()) {
            throw new \DomainException('Lead is already Processing with this owner.');
        }

        $this->recordEvent(
            new LeadProcessingEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->recordEvent(new LeadTaskEvent($this), LeadTaskEvent::class);

        $this->changeOwner($newOwnerId);

        if (!$this->isProcessing()) {
            $this->setStatus(self::STATUS_PROCESSING);
        }

        $description = $reason ? 'Reason: ' . $reason . '. ' : '';
        if (($fromStatus = self::getStatus($oldStatus)) && $toStatus = self::getStatus(self::STATUS_PROCESSING)) {
            $description .= sprintf(LeadPoorProcessingLogStatus::REASON_CHANGE_STATUS, $fromStatus, $toStatus);
        }

        $this->recordEvent(
            new LeadPoorProcessingEvent(
                $this,
                [
                    LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
                    LeadPoorProcessingDataDictionary::KEY_LAST_ACTION,
                    LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
                ],
                $description
            )
        );
        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) && $this->isBusinessType()) {
            $leadBusinessExtraQueueObjectDto = new LeadBusinessExtraQueueAbacDto($this);
            if (!$employee = Employee::find()->where(['id' => $this->employee_id])->limit(1)->one()) {
                throw new \RuntimeException('LeadOwner not found by ID(' . $this->employee_id . ')');
            }
            /** @abac LeadBusinessExtraQueueAbacDto, LeadBusinessExtraQueueAbacObject::PROCESS_ACCESS, LeadBusinessExtraQueueAbacObject::ACTION_PROCESS, Access to processing in business Extra Queue */
            if (Yii::$app->abac->can($leadBusinessExtraQueueObjectDto, LeadBusinessExtraQueueAbacObject::PROCESS_ACCESS, LeadBusinessExtraQueueAbacObject::ACTION_PROCESS, $employee)) {
                LeadBusinessExtraQueueService::addLeadBusinessExtraQueueJob(
                    $this,
                    'Added new Business Extra Queue Countdown'
                );
            }
        }
    }

    /**
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function pending(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_PENDING);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isPending()) {
            throw new \DomainException('Lead is already Pending without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isPending()) {
            throw new \DomainException('Lead is already Pending with this owner.');
        }

        $this->recordEvent(
            new LeadPendingEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isPending()) {
            $this->setStatus(self::STATUS_PENDING);
        }
    }

    /**
     * @param int|null $newOwnerId
     */
    private function changeOwner(?int $newOwnerId = null): void
    {
        if ($newOwnerId === null && $this->isFreedOwner()) {
            return;
        }

        if ($newOwnerId === null) {
            $this->freedOwner();
            return;
        }

        if (!$this->isOwner($newOwnerId)) {
            $this->setOwner($newOwnerId);
        }
    }

    /**
     * @param int|null $fromStatus
     * @param int $toStatus
     */
    private static function guardStatus(?int $fromStatus, int $toStatus): void
    {
        // todo
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function trash(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_TRASH);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isTrash()) {
            throw new \DomainException('Lead is already Trash without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isTrash()) {
            throw new \DomainException('Lead is already Trash with this owner.');
        }

        $this->recordEvent(
            new LeadTrashEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isTrash()) {
            $this->setStatus(self::STATUS_TRASH);
        }
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

    public function isReject(): bool
    {
        return $this->status === self::STATUS_REJECT;
    }

    public function isBookFailed(): bool
    {
        return $this->status === self::STATUS_BOOK_FAILED;
    }

    public function isAlternative(): bool
    {
        return $this->status === self::STATUS_ALTERNATIVE;
    }

    /**
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function reject(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_REJECT);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isReject()) {
            throw new \DomainException('Lead is already Reject without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isReject()) {
            throw new \DomainException('Lead is already Reject with this owner.');
        }

        $this->recordEvent(
            new LeadRejectEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isReject()) {
            $this->setStatus(self::STATUS_REJECT);
        }
    }

    public function new(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        self::guardStatus($this->status, self::STATUS_NEW);

        if ($newOwnerId === null && !$this->hasOwner() && $this->isNew()) {
            throw new \DomainException('Lead is already New without owner.');
        }

        if ($this->isOwner($newOwnerId) && $this->isNew()) {
            throw new \DomainException('Lead is already New with this owner.');
        }

        $this->recordEvent(
            new LeadNewEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        if (!$this->isNew()) {
            $this->setStatus(self::STATUS_NEW);
        }
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function callPrepare(): void
    {
        $this->setCallStatus(self::CALL_STATUS_PREPARE);
    }

    public function isCallPrepare(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_PREPARE;
    }

    public function callBugged(): void
    {
        $this->setCallStatus(self::CALL_STATUS_BUGGED);
    }

    public function isCallBugged(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_BUGGED;
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

    public function callCancel(): void
    {
        $this->setCallStatus(self::CALL_STATUS_CANCEL);
    }

    /**
     * @return bool
     */
    public function isCallCancel(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_CANCEL;
    }

    public function callQueue(): void
    {
        $this->setCallStatus(self::CALL_STATUS_QUEUE);
    }

    /**
     * @return bool
     */
    public function isCallQueue(): bool
    {
        return $this->l_call_status_id === self::CALL_STATUS_QUEUE;
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

    /**
     * @param int $originId
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function duplicate(?int $originId, ?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        $this->l_duplicate_lead_id = $originId;
        $this->trash($newOwnerId, $creatorId, $reason ?: 'Duplicate. OriginId: ' . $originId);
        $this->recordEvent(new LeadDuplicateDetectedEvent($this));
    }

    public function setVisitorLog(int $logId): void
    {
        $this->l_visitor_log_id = $logId;
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
        return in_array(
            $this->status,
            [
                null,
                self::STATUS_TRASH,
                self::STATUS_PENDING,
                self::STATUS_FOLLOW_UP,
                self::STATUS_SNOOZE,
                self::STATUS_NEW,
                self::STATUS_BOOK_FAILED,
                self::STATUS_ALTERNATIVE,
                self::STATUS_EXTRA_QUEUE,
                self::STATUS_BUSINESS_EXTRA_QUEUE,
            ],
            true
        );
    }

    /**
     * @return bool
     */
    public function isAvailableToProcessing(): bool
    {
        return $this->isAvailableToTake() || $this->isAvailableToTakeOver();
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
            'project_id' => 'Project',
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
            'l_delayed_charge' => 'Delayed charge',
            'hybrid_uid' => 'Booking ID',

            'l_visitor_log_id' => 'Visitor log ID',
            'l_status_dt' => 'Status Dt',
            'l_expiration_dt' => 'Expiration',
            'l_type' => 'Type',
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
            'metric' => [
                'class' => MetricLeadCounterBehavior::class,
            ],
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getLDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'l_dep_id']);
    }

    public function getMinLpp()
    {
        return $this->hasOne(LeadPoorProcessing::class, ['lpp_lead_id' => 'id'])->orderBy(['lpp_expiration_dt' => SORT_ASC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCalls()
    {
        return $this->hasMany(Call::class, ['c_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::class, ['e_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadCallExperts()
    {
        return $this->hasMany(LeadCallExpert::class, ['lce_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadChecklists(): ActiveQuery
    {
        return $this->hasMany(LeadChecklist::class, ['lc_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLDuplicateLead(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'l_duplicate_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeads0()
    {
        return $this->hasMany(self::class, ['l_duplicate_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadFlows()
    {
        return $this->hasMany(LeadFlow::class, ['lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadUserRatings()
    {
        return $this->hasMany(LeadUserRating::class, ['lur_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadUserRatingByUser(int $userId)
    {
        return $this->hasOne(LeadUserRating::class, ['lur_lead_id' => 'id'])->onCondition(['lur_user_id' => $userId]);
    }


    /*
     * @return int
     */
    public function getLeadUserRatingValueByUserId(int $userId): int
    {
        try {
            return (int)ArrayHelper::getValue($this->getLeadUserRatingByUser($userId)->one(), 'lur_rating', 0);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'LeadModel:getLeadUserRatingValueByUserId:Throwable');
            return 0;
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadFlowSold()
    {
        return $this->hasOne(LeadFlow::class, ['lead_id' => 'id'])->onCondition([LeadFlow::tableName() . '.status' => static::STATUS_SOLD]);
    }

    // TODO: update LeadFlow SORT created -> id
    /**
     * @return ActiveQuery
     */
    public function getLastLeadFlow(): ActiveQuery
    {
        return $this->hasOne(LeadFlow::class, ['lead_id' => 'id'])->orderBy([LeadFlow::tableName() . '.created' => SORT_DESC, LeadFlow::tableName() . '.id' => SORT_DESC])->limit(1);
    }

    /**
     * @return ActiveQuery
     */
    public function getSms(): ActiveQuery
    {
        return $this->hasMany(Sms::class, ['s_lead_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getNotes(): ActiveQuery
    {
        return $this->hasMany(Note::class, ['lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffers(): ActiveQuery
    {
        return $this->hasMany(Offer::class, ['of_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrders(): ActiveQuery
    {
        return $this->hasMany(Order::class, ['or_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['pr_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTipsSplits(): ActiveQuery
    {
        return $this->hasMany(TipsSplit::class, ['ts_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
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
     * @return ActiveQuery
     */
    public function getUserConnections(): ActiveQuery
    {
        return $this->hasMany(UserConnection::class, ['uc_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadFlightSegments(): ActiveQuery
    {
        return $this->hasMany(LeadFlightSegment::class, ['lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadPreferences(): ActiveQuery
    {
        return $this->hasOne(LeadPreferences::class, ['lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadTasks()
    {
        return $this->hasMany(LeadTask::class, ['lt_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadQcall(): ActiveQuery
    {
        return $this->hasOne(LeadQcall::class, ['lqc_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getClone(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'clone_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProfitSplits(): ActiveQuery
    {
        return $this->hasMany(ProfitSplit::class, ['ps_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPsUsers()
    {
        return $this->hasMany(Employee::class, ['id' => 'ps_user_id'])->viaTable('profit_split', ['ps_lead_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSource(): ActiveQuery
    {
        return $this->hasOne(Sources::class, ['id' => 'source_id']);
    }

    /**
     * @return ActiveQuery
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
     * @return ActiveQuery
     */
    public function getLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'l_client_lang']);
    }

    public function getLeadData(): ActiveQuery
    {
        return $this->hasMany(LeadData::class, ['ld_lead_id' => 'id']);
    }

    public function getLeadUserConversion(): ActiveQuery
    {
        return $this->hasMany(LeadUserConversion::class, ['luc_lead_id' => 'id']);
    }

    public function updateLastAction(?string $description = null): int
    {
        $idKey = 'update_last_action_' . $this->id;

        if (RedisHelper::checkDuplicate($idKey, 5)) {
            \Yii::info(
                [
                    'message' => 'Checked Duplicate Update Last Action in Lead',
                    'leadId' => $this->id
                ],
                'Lead:updateLastAction:checkDuplicate'
            );
            return 0;
        }

        $result = self::updateAll(['l_last_action_dt' => date('Y-m-d H:i:s')], ['id' => $this->id]);

        if ($this->isProcessing()) {
            LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                $this->id,
                [
                    LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE,
                    LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD,
                    LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
                ],
                $description
            );
            LeadPoorProcessingService::addLeadPoorProcessingJob(
                $this->id,
                [LeadPoorProcessingDataDictionary::KEY_LAST_ACTION],
                $description
            );
        }

        return $result;
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
        if ($flow) {
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
            return self::getCountdownTimer(new DateTime($snooze_for), sprintf('snooze-countdown-%d', $id));
        }
        return '-';
    }

    /**
     * @param DateTime $expired
     * @param $spanId
     * @return string
     */
    public static function getCountdownTimer(DateTime $expired, $spanId): string
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
        $now = new DateTime();
        $lastUpdate = new DateTime($updated);
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

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if (!$user->isAdmin()) {
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
        return LeadFlow::find()->andWhere(['lead_id' => $this->id])->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->all();
    }

    public function getPendingAfterCreate($created = null)
    {
        $now = new DateTime();
        if (empty($created)) {
            $created = $this->created;
        }
        $created = new DateTime($created);
        return self::diffFormat($now->diff($created));
    }

    public static function getPendingInLastStatus($updated)
    {
        $now = new DateTime();
        $updated = new DateTime($updated);
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
            case self::STATUS_CLOSED:
                $label = '<span class="label status-label bg-red">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_NEW:
            case self::STATUS_BOOK_FAILED:
            case self::STATUS_ALTERNATIVE:
            case self::STATUS_EXTRA_QUEUE:
                $label = '<span class="label label-default">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_BUSINESS_EXTRA_QUEUE:
                $label = '<span class="label label-default">' . self::getStatus($status) . '</span>';
                break;
        }
        return $label;
    }

    public static function getStatusLabelForLeadFlow($status)
    {
        $label = '';
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
            case self::STATUS_CLOSED:
                $label = '<span class="label status-label bg-red">' . self::getStatus($status) . '</span>';
                break;
            case self::STATUS_NEW:
            case self::STATUS_BOOK_FAILED:
            case self::STATUS_ALTERNATIVE:
            case self::STATUS_EXTRA_QUEUE:
            case self::STATUS_BUSINESS_EXTRA_QUEUE:
                $label = '<span class="label label-default">' . self::getStatus($status) . '</span>';
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
            $class = self::getStatusLabelClass($this->status);
            $statusName = '<span class="label ' . $class . '" style="font-size: 13px">' . Html::encode($statusName) . '</span>';
        }

        return $statusName;
    }

    /**
     * @param int|null $status
     * @return string
     */
    public static function getStatusLabelClass(?int $status): string
    {
        return self::STATUS_CLASS_LIST[$status] ?? 'label-default';
    }


    private function updateGmtByFlight(): void
    {
        if (!$this->offset_gmt && $this->leadFlightSegments) {
            /*Yii::warning(['offset_gmt' => $this->offset_gmt, 'lead_id' => $this->id,
                'firstSegment' =>  VarDumper::dumpAsString($this->leadFlightSegments[0])], 'updateGmtByFlight-1');*/
            $firstSegment = $this->leadFlightSegments[0];
            $airport = Airports::findByIata($firstSegment->origin);
            if ($airport && is_numeric($airport->dst)) {
                $offset = $airport->dst;
                $offsetStr = null;

                if ($offset > 0) {
                    if ($offset < 10) {
                        $offsetStr = '+0' . $offset . ':00';
                    } else {
                        $offsetStr = '+' . $offset . ':00';
                    }
                } elseif ($offset < 0) {
                    if ($offset > -10) {
                        $offsetStr = '-0' . abs($offset) . ':00';
                    } else {
                        $offsetStr = $offset . ':00';
                    }
                } else {
                    $offsetStr = '-00:00';
                }

                if ($offsetStr) {
                    $this->offset_gmt = $offsetStr;
                    self::updateAll(['offset_gmt' => $this->offset_gmt], ['id' => $this->id]);
                    //Yii::warning(['offset_gmt' => $this->offset_gmt, 'lead_id' => $this->id], 'updateGmtByFlight-2');
                }
            }
        }
    }

    /**
     * @return array
     */
    public function updateIpInfo(): array
    {
        $out = ['error' => false, 'data' => []];

        if (empty($this->offset_gmt)) {
            if (empty($this->request_ip)) {
                $this->updateGmtByFlight();
            } else {
                $ip = $this->request_ip; //'217.26.162.22';
                $key = Yii::$app->params['ipinfodb_key'] ?? '';

                if (!$key) {
                    Yii::warning('Params ipinfodb_key is empty', 'Lead:updateIpInfo');
                }
                $url = 'http://api.ipinfodb.com/v3/ip-city/?format=json&key=' . $key . '&ip=' . $ip;

                $ctx = stream_context_create(['http' =>
                    ['timeout' => 5]  //Seconds
                ]);

                try {
                    $jsonData = file_get_contents($url, false, $ctx);

                    if ($jsonData) {
                        $data = @json_decode($jsonData, true);


                        if ($data && isset($data['timeZone'])) {
                            if (isset($data['statusCode'])) {
                                unset($data['statusCode']);
                            }

                            if (isset($data['statusMessage'])) {
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
                    $this->updateGmtByFlight();
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

        $host = \Yii::$app->params['url'];

        if ($type && $employee_id && isset(Yii::$app->params['email_from']['sales'])) {
            $user = Employee::findOne($employee_id);

            if ($employee2_id) {
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
                    $body = Yii::t(
                        'email',
                        "Attention!
Your Lead ({lead_id}) has been reassigned to another agent ({name2}).",
                        [
                            'name' => $userName,
                            'name2' => $userName2,
                            'lead_id' => Purifier::createLeadShortLink($this),
                            'br' => "\r\n"
                        ]
                    );

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

                    $body = Yii::t(
                        'email',
                        "Booked quote with UID : {quote_uid},
Source: {name},
Lead: {lead_id}
{name} made \${profit} on {airline} to {destination}",
                        [
                            'name' => $userName,
                            'lead_id' => Purifier::createLeadShortLink($this),
                            'quote_uid' => $quote ? $quote->uid : '-',
                            'destination' => $flightSegment ? $flightSegment->destination : '-',
                            'airline' => $airlineName,
                            'profit' => $profit,
                            'br' => "\r\n"
                        ]
                    );

                    $subject = Yii::t('email', 'Lead-{id} to SOLD', ['id' => $this->id]);
                } elseif ($type === 'lead-status-booked') {
                    $subject = Yii::t('email', 'Lead-{id} to BOOKED', ['id' => $this->id]);
                    $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

                    $body = Yii::t(
                        'email',
                        "Your Lead ({lead_id}) has been changed status to BOOKED!
Booked quote UID: {quote_uid}",
                        [
                            'name' => $userName,
                            'lead_id' => Purifier::createLeadShortLink($this),
                            'quote_uid' => $quote ? $quote->uid : '-',
                            'br' => "\r\n"
                        ]
                    );
                } elseif ($type === 'lead-status-snooze') {
                    $subject = Yii::t('email', "Lead-{id} to SNOOZE", ['id' => $this->id]);
                    $body = Yii::t(
                        'email',
                        "Your Lead ({lead_id}) has been changed status to SNOOZE!
Snooze for: {datetime}.
Reason: {reason}",
                        [
                            'name' => $userName,
                            'lead_id' => Purifier::createLeadShortLink($this),
                            'datetime' => Yii::$app->formatter->asDatetime(strtotime($this->snooze_for)),
                            'reason' => $this->status_description ?: '-',
                            'br' => "\r\n"
                        ]
                    );
                } elseif ($type === 'lead-status-follow-up') {
                    $subject = Yii::t('email', "Lead-{id} to FOLLOW-UP", ['id' => $this->id]);
                    $body = Yii::t(
                        'email',
                        'Your Lead ({lead_id}) has been changed status to FOLLOW-UP!
Reason: {reason}',
                        [
                            'name' => $userName,
                            'reason' => $this->status_description ?: '-',
                            'lead_id' => Purifier::createLeadShortLink($this),
                            'br' => "\r\n"
                        ]
                    );
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

                    if ($ntf = Notifications::create($user->id, $subject, $body, Notifications::TYPE_INFO, true)) {
                        $isSend = true;
                        // Notifications::socket($user->id, null, 'getNewNotification', [], true);
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $user->id], $dataNotification);
                    }


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

    public function disableAREvents(): void
    {
        $this->enableActiveRecordEvents = false;
    }

    /**
     * @return int
     */
    public function getCountOutCallsLastFlow(): int
    {
        if ($this->lastLeadFlow) {
            return (int)$this->lastLeadFlow->lf_out_calls;
        }
        return 0;
    }

    /**
     * @param string $phoneNumber
     * @param int|null $project_id
     * @param bool $sql
     * @return array|string|ActiveRecord|null
     */
    public static function findLastLeadByClientPhone(string $phoneNumber = '', ?int $project_id = null, bool $sql = false)
    {
        $query = self::find()->innerJoinWith(['client.clientPhones'])
            ->where(['client_phone.phone' => $phoneNumber])
            ->andWhere(['<>', 'leads.status', self::STATUS_TRASH])
            ->andWhere([
                'OR',
                ['IS', 'client_phone.type', null],
                ['!=', 'client_phone.type', ClientPhone::PHONE_INVALID],
            ])
            ->orderBy(['leads.id' => SORT_DESC])
            ->limit(1);

        if ($project_id) {
            $query->andWhere(['leads.project_id' => $project_id]);
        }

        return $sql ? $query->createCommand()->getRawSql() : $query->one();
    }

    /**
     * @param string|null $phoneNumber
     * @param int|null $project_id
     * @param int|null $source_id
     * @param $gmt
     * @return static
     */
    public static function createNewLeadByPhone(?string $phoneNumber, ?int $project_id, ?int $source_id, $gmt): self
    {
        $lead = new self();
        $lead->l_client_phone = $phoneNumber;
        $lead->l_type_create = self::TYPE_CREATE_INCOMING_CALL;

        $clientPhone = ClientPhone::find()->where(['phone' => $phoneNumber])->orderBy(['id' => SORT_DESC])->limit(1)->one();

        if ($clientPhone) {
            $client = $clientPhone->client;
        } else {
            $client = new Client();
            $client->first_name = 'ClientName';
            $client->created = date('Y-m-d H:i:s');

            if ($client->save()) {
                $clientPhone = ClientPhone::create($phoneNumber, $client->id, null, 'incoming');
                try {
                    $clientPhoneRepository = Yii::createObject(ClientPhoneRepository::class);
                    $clientPhoneRepository->save($clientPhone);
                } catch (\RuntimeException $e) {
                    Yii::error(VarDumper::dumpAsString($clientPhone->errors), 'Model:Lead:createNewLeadByPhone:ClientPhone:save');
                }
            }
        }

        if ($client) {
            $lead->status = self::STATUS_PENDING;
            //$lead->employee_id = $this->c_created_user_id;
            $lead->client_id = $client->id;
            $lead->project_id = $project_id;
            $lead->source_id = $source_id;
            $lead->l_call_status_id = self::CALL_STATUS_QUEUE;
            $lead->offset_gmt = $gmt;
            $source = null;

            if ($source_id) {
                $source = Sources::findOne(['id' => $lead->source_id]);
            }

            if (!$source) {
                $source = Sources::find()->select('id')->where(['project_id' => $lead->project_id, 'default' => true])->one();
            }

            if ($source) {
                $lead->source_id = $source->id;
            }

            if ($lead->save()) {
                /*self::updateAll(['c_lead_id' => $lead->id], ['c_id' => $this->c_id]);

                if($lead->employee_id) {
                    $task = Task::find()->where(['t_key' => Task::TYPE_MISSED_CALL])->limit(1)->one();

                    if ($task) {
                        $lt = new LeadTask();
                        $lt->lt_lead_id = $lead->id;
                        $lt->lt_task_id = $task->t_id;
                        $lt->lt_user_id = $lead->employee_id;
                        $lt->lt_date = date('Y-m-d');
                        if (!$lt->save()) {
                            Yii::error(VarDumper::dumpAsString($lt->errors), 'Model:Lead:createNewLeadByPhone:LeadTask:save');
                        }
                    }
                }*/
            } else {
                Yii::error(VarDumper::dumpAsString($lead->errors), 'Model:Lead:createNewLeadByPhone:Lead:save');
            }
        }

        return $lead;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->enableActiveRecordEvents) {
            if ($insert) {
                LeadFlow::addStateFlow($this);

                if ($this->scenario === self::SCENARIO_API) {
                    try {
                        $service = Yii::createObject(QCallService::class);
                        $service->create(
                            $this->id,
                            new Config(
                                $this->status,
                                $this->getCountOutCallsLastFlow()
                            ),
                            new FindWeightParams($this->project_id, $this->status),
                            $this->offset_gmt,
                            new FindPhoneParams($this->project_id, $this->l_dep_id)
                        );
                    } catch (\Throwable $e) {
                        Yii::error($e, 'Lead:AfterSave:QCallService:create');
                    }
                }

                /*$job = new QuickSearchInitPriceJob();
                $job->lead_id = $this->id;
                $jobId = Yii::$app->queue_job->push($job);*/

                //Yii::info('Lead: ' . $this->id . ', QuickSearchInitPriceJob: '.$jobId, 'info\Lead:afterSave:QuickSearchInitPriceJob');
            } else {
                if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {
                    LeadFlow::addStateFlow($this);

                    if ($this->called_expert && ($this->status == self::STATUS_TRASH || $this->status == self::STATUS_FOLLOW_UP || $this->status == self::STATUS_SNOOZE || $this->status == self::STATUS_PROCESSING)) {
                        $job = new UpdateLeadBOJob();
                        $job->lead_id = $this->id;
                        $jobId = Yii::$app->queue_job->priority(200)->push($job);
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
                        if ($this->status_description) {
                            //todo delete
//                            $reason = new Reason();
//                            $reason->lead_id = $this->id;
//                            $reason->employee_id = $this->employee_id;
//                            $reason->created = date('Y-m-d H:i:s');
//                            $reason->reason = $this->status_description;
//                            $reason->save();
                        }

                        /*if (!$this->sendNotification('lead-status-booked', $this->employee_id, null, $this)) {
                            Yii::warning('Not send Email notification to employee_id: ' . $this->employee_id . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                        }*/
                    } elseif ($this->status == self::STATUS_SNOOZE) {
                        if ($this->status_description) {
                            //todo delete
//                            $reason = new Reason();
//                            $reason->lead_id = $this->id;
//                            $reason->employee_id = $this->employee_id;
//                            $reason->created = date('Y-m-d H:i:s');
//                            $reason->reason = $this->status_description;
//                            $reason->save();
                        }


                        if ($this->employee_id && !$this->sendNotification('lead-status-snooze', $this->employee_id, null, $this)) {
                            Yii::warning('Not send Email notification to employee_id: ' . $this->employee_id . ', lead: ' . $this->id, 'Lead:afterSave:sendNotification');
                        }
                    }
                }
            }

            //create or update LeadTask
            if (
                ($this->status == self::STATUS_PROCESSING && isset($changedAttributes['status'])) ||
                (isset($changedAttributes['employee_id']) && $this->status == self::STATUS_PROCESSING) ||
                (isset($changedAttributes['l_answered']) && $changedAttributes['l_answered'] != $this->l_answered)
            ) {
                LeadTask::deleteUnnecessaryTasks($this->id);

                if ($this->l_answered) {
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
//        $leadLog = new LeadLog(new LeadLogMessage());
//        $leadLog->logMessage->oldParams = $changedAttributes;
//        $leadLog->logMessage->newParams = array_intersect_key($this->attributes, $changedAttributes);
//        $leadLog->logMessage->title = ($insert)
//            ? 'Create' : 'Update';
//        $leadLog->logMessage->model = $this->formName();
//        $leadLog->addLog([
//            'lead_id' => $this->id,
//        ]);
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
            $airport = Airports::findByIata($firstSegment->origin);
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
     * @return DateTime|null
     * @throws \Exception
     */
    public function getClientTime2(): ?DateTime
    {
        $clientDt = null;
        $offset = false;

        if ($this->offset_gmt) {
            $offset = str_replace('.', ':', $this->offset_gmt);
        } elseif ($this->leadFlightSegments) {
            $firstSegment = $this->leadFlightSegments[0] ?? null;
            if ($firstSegment && ($airport = Airports::findByIata($firstSegment->origin)) && $airport->dst) {
                $offset = $airport->dst;
            }
        }

        if ($offset) {
            if (is_numeric($offset) && $offset > 0) {
                $offset = '+' . $offset;
            }
            $clientDt = new DateTime();
            $timezoneName = timezone_name_from_abbr('', (int)$offset * 3600, date('I', time()));
            if ($timezoneName) {
                $timezone = new \DateTimeZone($timezoneName);
                $clientDt->setTimezone($timezone);
            }
        }

        return $clientDt;
    }

    /**
     * @return DateTime|null
     * @throws \Exception
     */
    public function getClientTime2Old()
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
            $airport = Airports::findByIata($firstSegment->origin);
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

            if (is_numeric($offset) && $offset > 0) {
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


            $timezoneName = timezone_name_from_abbr('', (int)$offset * 3600, date('I', time()));

            /*$date = new \DateTime(time(), new \DateTimeZone($timezoneName));
           // $clientTime = Yii::$app->formatter->asTime() $date->format('H:i');
            $clientTime = $date->format('H:i');



            $utcTime  = new \DateTime('now', new \DateTimeZone('UTC'));


            $gmtTimezone = new \DateTimeZone($timezoneName);
            $myDateTime = new \DateTime('2019-02-18 13:28', $gmtTimezone);





            $clientTime = $utcTime->format('H:i');*/



            //-----------------------------------------------------------


            $dt = new DateTime();
            if ($timezoneName) {
                $timezone = new \DateTimeZone($timezoneName);
                $dt->setTimezone($timezone);
            }
            $clientTime =  $dt->format('H:i');


            //$clientTime = $clientTime; . ' '.$timezone->getName();  //$offset

            $clientTime = '<b title="TZ (' . $offset . ') ' . ($this->offset_gmt ? 'by IP' : 'by IATA') . '"><i class="fa fa-clock-o ' . ($this->offset_gmt ? 'success' : '') . '"></i> ' . Html::encode($clientTime) . '</b>'; //<br/>(GMT: ' .$offset_gmt . ')';

            //$clientTime = $offset;
        }

        return $clientTime;
    }


    public function getSentCount()
    {
        $data = Quote::find()
            ->where(['lead_id' => $this->id, 'status' => [
                Quote::STATUS_SENT,
                Quote::STATUS_OPENED,
                Quote::STATUS_APPLIED]
            ])->all();
        return count($data);
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
        return $this->hasOne(Quote::class, ['lead_id' => 'id'])->andWhere(
            [
            'or',
            [Quote::tableName() . '.status' => Quote::STATUS_APPLIED],
            [Quote::tableName() . '.status' => null]]
        );
    }

    public function hasAppliedQuote(): bool
    {
        return Quote::find()->where(['lead_id' => $this->id, 'status' => Quote::STATUS_APPLIED])->exists();
    }

    public function getFirstFlightSegment()
    {
        return LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['departure' => 'ASC'])->one();
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function getLastFlightSegment()
    {
        return LeadFlightSegment::find()->where(['lead_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if (!$this->gid) {
                $this->gid = self::generateGid();
            }

            if ($insert) {
                //$this->created = date('Y-m-d H:i:s');
                if (!empty($this->project_id) && empty($this->source_id)) {
                    Yii::info([
                        'gid' => $this->gid,
                        'projectId' => $this->project_id,
                        'typeCreate' => $this->getTypeCreateName()
                    ], 'info\Lead:beforeSave:emptySourceIdOnLeadCreation');
                    $source = SourcesQuery::getDefaultSourceByProjectId($this->project_id) ?? SourcesQuery::getFirstSourceByProjectId($this->project_id);
                    $this->source_id = $source->id ?? null;
                }

                $leadExistByUID = Lead::findOne([
                    'uid' => $this->uid,
                    'source_id' => $this->source_id
                ]);
                if ($leadExistByUID !== null) {
                    $this->uid = self::generateUid();
                }

                /*if(!$this->gid) {
                    $this->gid = md5(uniqid('', true));
                }*/
            } else {
                //$this->updated = date('Y-m-d H:i:s');
            }

            if (!$this->uid) {
                $this->uid = self::generateUid();
            }

            $this->adults = (int) $this->adults;
            $this->children = (int) $this->children;
            $this->infants = (int) $this->infants;

            if (($this->isBooked() || $this->isSold()) && $quote = $this->getBookedQuote()) {
                $this->agents_processing_fee = $quote->agent_processing_fee;
            } else {
                $this->agents_processing_fee = ($this->adults + $this->children) * SettingHelper::processingFee();
            }
            $this->oldAdditionalInformation = $this->oldAttributes['additional_information'] ?? '';
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
            foreach ($this->getAdditionalInformationForm() as $additionalInformation) {
                $separateInfo[] = $additionalInformation->attributes;
            }
            $this->additional_information = json_encode($separateInfo);
        }

        parent::afterValidate();
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return parent::save($runValidation, $attributeNames);
    }

    public function sendNotifOnProcessingStatusChanged(): void
    {
        $additionalInformation = $this->additionalInformationFormFirstElement;
        $oldAdditionalInformation = $this->oldAdditionalInformationFormFirstElement;

        $vfy = 'VFY: ' . self::PROCESSED_VTF[(int)$oldAdditionalInformation->vtf_processed];
        $exp = 'EXP: ' . self::PROCESSED_EXP[(int)$oldAdditionalInformation->exp_processed];
        $tkt = 'TKT: ' . self::PROCESSED_TKT[(int)$oldAdditionalInformation->tkt_processed];
        $changed = false;
        if ($additionalInformation->vtf_processed !== $oldAdditionalInformation->vtf_processed) {
            $vfy .= ' -> ' . self::PROCESSED_VTF[(int)$additionalInformation->vtf_processed];
            $changed = true;
        }
        if ($additionalInformation->tkt_processed !== $oldAdditionalInformation->tkt_processed) {
            $tkt .= ' -> ' . self::PROCESSED_TKT[(int)$additionalInformation->tkt_processed];
            $changed = true;
        }
        if ($additionalInformation->exp_processed !== $oldAdditionalInformation->exp_processed) {
            $exp .= ' -> ' . self::PROCESSED_EXP[(int)$additionalInformation->exp_processed];
            $changed = true;
        }

        if ($changed) {
            $body = Yii::t(
                'notifications',
                "Booking status of the Lead ({lead_id}) has changed to: {br}{exp}{br}{vfy}{br}{tkt}",
                [
                    'lead_id' => Purifier::createLeadShortLink($this),
                    'br' => "\r\n",
                    'exp' => $exp,
                    'vfy' => $vfy,
                    'tkt' => $tkt
                ]
            );
            $subject = Yii::t('notifications', 'Lead Update ({lead_id})', ['lead_id' => $this->id]);
            if ($ntf = Notifications::create($this->employee_id, $subject, $body, Notifications::TYPE_INFO, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $this->employee_id], $dataNotification);
            }
        }
    }

    public function update($runValidation = true, $attributeNames = null)
    {
        return parent::update($runValidation, $attributeNames); // TODO: Change the autogenerated stub
    }

//    public function afterFind()
//    {
//        parent::afterFind();

//        if (!empty($this->additional_information)) {
//            $this->additionalInformationForm = self::getLeadAdditionalInfo($this->additional_information);
//        }

//        $this->totalTips = $this->tips ? $this->tips/2 : 0;

//        $processing_fee_per_pax = self::AGENT_PROCESSING_FEE_PER_PAX;
//
//        if($this->employee_id && $this->employee) {
//            $groups = $this->employee->ugsGroups;
//            if($groups) {
//                foreach ($groups as $group) {
//                    if($group->ug_processing_fee) {
//                        $processing_fee_per_pax = $group->ug_processing_fee;
//                        break;
//                    }
//                }
//                unset($groups);
//            }
//        }
//
//        if($this->final_profit !== null) {
//            $this->finalProfit = (float) $this->final_profit - ($processing_fee_per_pax * (int) ($this->adults + $this->children));
//        } else {
//            $this->finalProfit = $this->final_profit;
//        }

//        $this->agentProcessingFee = $processing_fee_per_pax * (int) ($this->adults + $this->children);
//        $this->agents_processing_fee = ($this->agents_processing_fee)?$this->agents_processing_fee:$processing_fee_per_pax * (int) ($this->adults + $this->children);
//    }

    private $totalTips;

    public function getTotalTips()
    {
        if ($this->totalTips !== null) {
            return $this->totalTips;
        }

        $this->totalTips = $this->tips ? $this->tips / 2 : 0;

        return $this->totalTips;
    }

    private $agentsProcessingFee;

    public function getAgentsProcessingFee()
    {
        if ($this->agentsProcessingFee !== null) {
            return $this->agentsProcessingFee;
        }

        $this->agentsProcessingFee = !is_null($this->agents_processing_fee) ? $this->agents_processing_fee : ($this->getProcessingFeePerPax() * (int)($this->adults + $this->children));

        return $this->agentsProcessingFee;
    }

    private $finalProfit;

    public function getFinalProfit()
    {
        if ($this->finalProfit !== null) {
            return $this->finalProfit;
        }

        if ($this->final_profit !== null) {
            $processingFee = !is_null($this->agents_processing_fee) ? $this->agents_processing_fee : ($this->getProcessingFeePerPax() * (int)($this->adults + $this->children));
            $this->finalProfit = (float)$this->final_profit - $processingFee;
        } else {
            $this->finalProfit = null;
        }

        return $this->finalProfit;
    }

    private $processingFeePerPax;

    public function getProcessingFeePerPax()
    {
        if ($this->processingFeePerPax !== null) {
            return $this->processingFeePerPax;
        }

        $quote = $this->getBookedQuote();
        if ($quote && $quote->isCreatedFromSearch()) {
            $this->processingFeePerPax = SettingHelper::quoteSearchProcessingFee();
        } else {
            $this->processingFeePerPax = SettingHelper::processingFee();
        }

//        if ($this->employee_id && $this->employee) {
//            $groups = $this->employee->ugsGroups;
//            if ($groups) {
//                foreach ($groups as $group) {
//                    if ($group->ug_processing_fee) {
//                        $this->processingFeePerPax = $group->ug_processing_fee;
//                        break;
//                    }
//                }
//                unset($groups);
//            }
//        }

        return $this->processingFeePerPax;
    }

    /**
     * @param $additionalInfoStr
     * @return LeadAdditionalInformation[]
     */
    public static function getLeadAdditionalInfo($additionalInfoStr)
    {
        $additionalInformationFormArr = [];
        $separateInfoArr = json_decode($additionalInfoStr);
        if (is_array($separateInfoArr) && !empty($separateInfoArr)) {
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

//    public function sendSoldEmail($data)
//    {
//        $result = [
//            'status' => false,
//            'errors' => []
//        ];
//
//        $key = sprintf('%s_lead_UID_%s', uniqid(), $this->uid);
//        $fileName = sprintf('_%s_%s.php', str_replace(' ', '_', strtolower($this->project->name)), $key);
//        $path = sprintf('%s/frontend/views/tmpEmail/quote/%s', dirname(Yii::getAlias('@app')), $fileName);
//
//        $template = ProjectEmailTemplate::findOne([
//            'type' => ProjectEmailTemplate::TYPE_EMAIL_TICKET,
//            'project_id' => $this->project_id
//        ]);
//
//        if ($template === null) {
//            $result['errors'][] = sprintf('Email Template [%s] for project [%s] not fond.',
//                ProjectEmailTemplate::getTypes(ProjectEmailTemplate::TYPE_EMAIL_TICKET),
//                $this->project->name
//            );
//            return $result;
//        }
//
//        $view = $template->template;
//        $fp = fopen($path, "w");
//        chmod($path, 0777);
//        fwrite($fp, $view);
//        fclose($fp);
//
//        $body = \Yii::$app->getView()->renderFile($path, [
//            'model' => $this,
//            'uid' => $this->getAppliedAlternativeQuotes()->uid,
//            'flightRequest' => $data,
//        ]);
//
//        $userProjectParams = UserProjectParams::findOne([
//            'upp_user_id' => $this->employee->id,
//            'upp_project_id' => $this->project_id
//        ]);
//        $credential = [
//            'email' => $userProjectParams->upp_email,
//        ];
//
//        if (!empty($template->layout_path)) {
//            $body = \Yii::$app->getView()->renderFile($template->layout_path, [
//                'project' => $this->project,
//                'agentName' => ucfirst($this->employee->username),
//                'employee' => $this->employee,
//                'userProjectParams' => $userProjectParams,
//                'body' => $body,
//                'templateType' => $template->type,
//            ]);
//        }
//
//        $subject = ProjectEmailTemplate::getMessageBody($template->subject, [
//            'pnr' => $data['pnr'],
//        ]);
//
//        $errors = [];
//        $bcc = [
//            trim($userProjectParams->upp_email),
//            'damian.t@wowfare.com',
//            'andrew.t@wowfare.com'
//        ];
//        $isSend = EmailService::sendByAWS($data['emails'], $this->project, $credential, $subject, $body, $errors, $bcc);
//        $message = ($isSend)
//            ? sprintf('Sending email - \'Tickets\' succeeded! <br/>Emails: %s',
//                implode(', ', $data['emails'])
//            )
//            : sprintf('Sending email - \'Tickets\' failed! <br/>Emails: %s',
//                implode(', ', $data['emails'])
//            );
//
//        //Add logs after changed model attributes
//        $leadLog = new LeadLog((new LeadLogMessage()));
//        $leadLog->logMessage->message = empty($errors)
//            ? $message
//            : sprintf('%s <br/>Errors: %s', $message, print_r($errors, true));
//        $leadLog->logMessage->title = 'Send Tickets by Email';
//        $leadLog->logMessage->model = $this->formName();
//        $leadLog->addLog([
//            'lead_id' => $this->id,
//        ]);
//
//        $result['status'] = $isSend;
//        $result['errors'] = $errors;
//
//        unlink($path);
//
//        return $result;
//    }

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
            $result['errors'][] = sprintf(
                'Email Template [%s] for project [%s] not fond.',
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

        $airport = Airports::findByIata($this->leadFlightSegments[0]->origin);
        $origin = ($airport !== null)
            ? $airport->city :
            $this->leadFlightSegments[0]->origin;

        $airport = Airports::findByIata($this->leadFlightSegments[0]->destination);
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


    /**
     * @param array $quoteIds
     * @param $projectContactInfo
     * @param string|null $lang
     * @param array $agent
     * @param Employee|null $employee
     * @return array
     * @throws \Exception
     */
    public function getEmailData2(array $quoteIds, $projectContactInfo, ?string $lang = null, array $agent = [], ?Employee $employee = null): array
    {
        $employee = $employee ?? Yii::$app->user->identity;
        $project = $this->project;

        $uppQuery = UserProjectParams::find()->where(['upp_project_id' => $project->id, 'upp_user_id' => $employee->id])->withEmailList()->withPhoneList();
        $upp = $this->project ? $uppQuery->one() : null;

        if ($quoteIds && is_array($quoteIds)) {
            foreach ($quoteIds as $qid) {
                $quoteModel = Quote::findOne($qid);
                if ($quoteModel) {
                    $quoteItem = [
                        'id' => $quoteModel->id,
                        'uid' => $quoteModel->uid,
                        'cabinClass' => $quoteModel->cabin,
                        'tripType' => $quoteModel->trip_type,
                        'hasSeparates' =>  $quoteModel->getTicketSegments() ? true : false
                    ];

                    $quoteItem = array_merge($quoteItem, $quoteModel->getInfoForEmail2($lang));

                    if ($quoteModel->providerProject && $quoteModel->providerProject->contact_info) {
                        $providerProjectContactInfo = JsonHelper::decode($quoteModel->providerProject->contact_info);
                        $quoteItem['provider'] = [
                            'name' => ArrayHelper::getValue($quoteModel->providerProject, 'name', ''),
                            'url' => ArrayHelper::getValue($quoteModel->providerProject, 'link', 'https://'),
                            'address' => ArrayHelper::getValue($providerProjectContactInfo, 'address', ''),
                            'phone' => ArrayHelper::getValue($providerProjectContactInfo, 'phone', ''),
                            'email' => ArrayHelper::getValue($providerProjectContactInfo, 'email', ''),
                        ];
                    }

                    $content_data['quotes'][] = $quoteItem;
                }
            }
            // sorting quotes by pricePerPax asc
            if (isset($content_data['quotes']) && is_array($content_data['quotes'])) {
                usort($content_data['quotes'], fn($a, $b) => (($a['pricePerPax'] ?? 0) - ($b['pricePerPax'] ?? 0)));
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
            'name'  => array_key_exists('full_name', $agent) ? $agent['full_name'] : $employee->full_name,
            'username'  => array_key_exists('username', $agent) ? $agent['username'] : $employee->username,
            'nickname' => array_key_exists('nickname', $agent) ? $agent['nickname'] : $employee->nickname,
            'phone' => $upp && $upp->getPhone() ? $upp->getPhone() : '',
            'email' => $upp && $upp->getEmail() ? $upp->getEmail() : '',
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

        if ($leadSegments = $this->leadFlightSegments) {
            $firstSegment = $leadSegments[0];
            $lastSegment = end($leadSegments);

            $departIATA = $firstSegment->origin;
            $arriveIATA = $lastSegment->destination;

            $departCity = AirportLangService::getCityByIataAndLang($firstSegment->origin, $lang);
            $arriveCity = AirportLangService::getCityByIataAndLang($firstSegment->destination, $lang);

            /** @property string $origin
             * @property string $destination
             * @property string $departure
             * @property int $flexibility
             * @property string $flexibility_type
             * @property string $created
             * @property string $updated
             * @property string $origin_label
             * @property string $destination_label */
            foreach ($leadSegments as $segmentModel) {
                $requestSegments[] = [
                    'departureDate' => $segmentModel->departure,
                    'originIATA' => $segmentModel->origin,
                    'destinationIATA' => $segmentModel->destination,
                    'originCity' => AirportLangService::getCityByIataAndLang($segmentModel->origin, $lang),
                    'destinationCity' => AirportLangService::getCityByIataAndLang($segmentModel->destination, $lang),
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

    /**
     * @param array $offerIds
     * @param array $projectContactInfo
     * @return array
     */
    public function getOfferEmailData(array $offerIds, array $projectContactInfo): array
    {
        $project = $this->project;

        $upp = null;
        if ($project) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $project->id, 'upp_user_id' => Yii::$app->user->id])->withEmailList()->withPhoneList()->one();
        }

        if ($offerIds && is_array($offerIds)) {
            foreach ($offerIds as $ofId) {
                $offerModel = Offer::findOne($ofId);
                if ($offerModel) {
                    $offerItem = $offerModel->serialize(); //attributes;
                    //$quoteItem = array_merge($quoteItem, $offerModel->getInfoForEmail2());
                    $content_data['offers'][] = $offerItem;
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
            'nickname' => Yii::$app->user->identity->nickname,
//            'phone' => $upp && $upp->upp_tw_phone_number ? $upp->upp_tw_phone_number : '',
            'phone' => $upp && $upp->getPhone() ? $upp->getPhone() : '',
//            'email' => $upp && $upp->upp_email ? $upp->upp_email : '',
            'email' => $upp && $upp->getEmail() ? $upp->getEmail() : '',
        ];

        $content_data['client'] = [
            'fullName'     => $this->client ? $this->client->full_name : '',
            'firstName'    => $this->client ? $this->client->first_name : '',
            'lastName'     => $this->client ? $this->client->last_name : '',
        ];

        return $content_data;
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
            'agent_team' => $this->employee ? $this->employee->getUserGroupList() : [],
            'agent_id' => $this->employee_id,
            'delayed_charge' => $this->l_delayed_charge
        ];

        $itinerary = [];
        foreach ($this->leadFlightSegments as $leadFlightSegment) {
            $itinerary[] = [
                'route' => sprintf('%s - %s', $leadFlightSegment->origin, $leadFlightSegment->destination),
                'date' => $leadFlightSegment->departure,
                'flex' => empty($leadFlightSegment->flexibility)
                    ? '' : sprintf(
                        '%s %d',
                        $leadFlightSegment->flexibility_type,
                        $leadFlightSegment->flexibility
                    )
            ];
        }
        $information['itinerary'] = $itinerary;

        $quoteArr = [];

        if ($this->quotes) {
            foreach ($this->quotes as $quote) {
                $quoteArr[] = $quote->getQuoteInformationForExpert();
            }
        }


        $similarLeads = [];

        if ($cloneLead = $this->clone) {
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

        if ($childLeads) {
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
     * @param Employee $user
     * @return array
     */
    public static function getAllStatuses(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @param Employee $user
     * @return array
     */
    public static function getStatusList(Employee $user = null): array
    {
        if ($user !== null) {
            if ($user->isAdmin()) {
                $list = self::STATUS_LIST;
            } elseif ($user->isSupervision()) {
                $list = self::STATUS_MULTIPLE_UPDATE_LIST;
            } else {
                $list = self::STATUS_LIST;
            }
        } else {
            $list = self::STATUS_LIST;
        }

        if (isset($list[self::STATUS_ON_HOLD])) {
            unset($list[self::STATUS_ON_HOLD]);
        }

        return $list;
    }

    /**
     * @return array
     */
    public static function getProcessingStatuses(): array
    {
        $list = [
            self::STATUS_SNOOZE => self::STATUS_LIST[self::STATUS_SNOOZE],
            self::STATUS_PROCESSING => self::STATUS_LIST[self::STATUS_PROCESSING],
            self::STATUS_ON_HOLD => self::STATUS_LIST[self::STATUS_ON_HOLD],
            self::STATUS_EXTRA_QUEUE => self::STATUS_LIST[self::STATUS_EXTRA_QUEUE],
        ];

        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) === true) {
            $list[self::STATUS_BUSINESS_EXTRA_QUEUE] = self::STATUS_LIST[self::STATUS_BUSINESS_EXTRA_QUEUE];
        }

        return $list;
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

        echo $command->getRawSql();
        exit;

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
        /** @fflag FFlag::FF_KEY_CHANGE_QUERY_GET_SEND_QUOTE, Change query get send Quote in PQ, FollowUpQ */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CHANGE_QUERY_GET_SEND_QUOTE)) {
            $query
                ->select(
                    [
                        'total' => 'COUNT(*)',
                        'send_q' => "SUM((SELECT SUM(CASE WHEN (status = :status) THEN 1 ELSE 0 END)
                         FROM `quote_status_log` WHERE `q`.id = `quote_status_log`.quote_id))"
                    ]
                )
                ->addParams([':status' => Quote::STATUS_SENT])
                ->from(Quote::tableName() . ' q')
                ->where(['lead_id' => $this->id]);

            $result = $query->createCommand()->queryOne();
            $result['not_send_q'] = ((int)$result['total'] - (int)$result['send_q']);

            return $result;
        } else {
            $query->select(['SUM(CASE WHEN status IN (2, 4, 5) THEN 1 ELSE 0 END) AS send_q',
                'SUM(CASE WHEN status NOT IN (2, 4, 5) THEN 1 ELSE 0 END) AS not_send_q'])
                ->from(Quote::tableName() . ' q')
                ->where(['lead_id' => $this->id]);

            return $query->createCommand()->queryOne();
        }
    }

    public function getLastActivityByNote()
    {
        $lastNote = Note::find()->where(['lead_id' => $this->id])->orderBy(['created' => SORT_DESC])->one();

        if (!empty($lastNote)) {
            return $lastNote['created'];
        }

        return $this->updated;
    }

    /**
     * @return string
     */
    public function getLastReasonFromLeadFlow(): ?string
    {
        if (!$this->lastLeadFlow) {
            return '';
        }
        return $this->lastLeadFlow->lf_description; //$this->status === $this->lastLeadFlow->status ? $this->lastLeadFlow->lf_description : '';
    }

    /**
     * @param $params
     * @param array $quoteStatus
     * @return ActiveDataProvider
     */
    public function getQuotesProvider($params, array $quoteStatus = [])
    {
        $query = Quote::find()->where(['lead_id' => $this->id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'type_id' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ],
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

        $query->andFilterWhere(['status' => $quoteStatus]);

        return $dataProvider;
    }


    /**
     * @return string
     */
    public function generateLeadKey(): string
    {
        $leadFlights = $this->leadFlightSegments;
        $key = $this->cabin;
        foreach ($leadFlights as $flEntry) {
            $key .= $flEntry->origin . $flEntry->destination . strtotime($flEntry->departure) . $flEntry->flexibility_type . $flEntry->flexibility;
        }
        $key .= '_' . $this->adults . '_' . $this->children . '_' . $this->infants;
        return $key;
    }


    /**
     * @param int $type_id
     * @param bool|null $onlyParent
     * @return int
     */
    public function getCountCalls(int $type_id = 0, ?bool $onlyParent = true): int
    {
        $query = CallLogLead::find()
            ->innerJoin(CallLog::tableName(), 'call_log.cl_id = call_log_lead.cll_cl_id')
            ->where(['cll_lead_id' => $this->id]);

        if ($type_id !== 0) {
            $query->andWhere(['cl_type_id' => $type_id]);
        }
        return (int) $query->count();
    }

    /**
     * @param int $type_id
     * @return int
     */
    public function getCountSms(int $type_id = 0): int
    {
        $query = Sms::find();
        $query->where(['s_lead_id' => $this->id, 's_is_deleted' => false]);

        if ($type_id !== 0) {
            $query->andWhere(['s_type_id' => $type_id]);
        }

        $count = $query->count();
        return (int) $count;
    }


    /**
     * @param int $type_id
     * @return int
     */
    public function getCountEmails(int $type_id = 0): int
    {
        return EmailRepositoryFactory::getRepository()->getEmailCountForLead($this->id, $type_id);
    }

    /**
     * @return int
     */
    public function getCountClientChat(): int
    {
        return (int) ClientChatLead::find()
            ->where(['ccl_lead_id' => $this->id])
            ->count();
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
        $query->andWhere(['OR', ['BETWEEN', new Expression('TIME(CONVERT_TZ(NOW(), \'+00:00\', offset_gmt))'), '09:00', '21:00'], ['>=', 'created', date('Y-m-d H:i:s', strtotime('-' . self::PENDING_ALLOW_CALL_TIME_MINUTES . ' min'))]]);
        $query->andWhere(['OR', ['employee_id' => null], ['employee_id' => $user_id]]);

        if ($user_id) {
            $subQuery = UserProjectParams::find()
                ->select(['upp_project_id'])
                ->innerJoinWith('phoneList', false)
                ->where(['upp_user_id' => $user_id]);
//                ->andWhere([
//                    'AND',
//                    ['IS NOT', 'upp_tw_phone_number', null],
//                    ['<>', 'upp_tw_phone_number', '']
//                ]);

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

        if ($this->created) {
            $diffSeconds = time() - strtotime($this->created);
        } else {
            $diffSeconds = 0;
        }

        $hour = 60;

        $diffMin = ceil($diffSeconds / $hour);

        if ($diffMin < $hour) {
            $min = 10;
        } elseif ($diffMin < ($hour * 4)) {
            $min = 30;
        } elseif ($diffMin < ($hour * 72)) {
            $min = 180;
        } elseif ($diffMin < ($hour * 192)) {
            $min = 180;
        } /*else {
            $min = 120;
        }*/

        // 120 h - 192 h

        return $min;
    }

    /**
     * @param int $delayedCharged
     * @param string $notesForExperts
     * @param bool $delayChargeAccess
     */
    public function editDelayedChargeAndNote(int $delayedCharged, string $notesForExperts, bool $delayChargeAccess): void
    {
        if ($delayChargeAccess) {
            $this->l_delayed_charge = $delayedCharged;
        }

        $this->notes_for_experts = strip_tags($notesForExperts);
    }

    /**
     * @return LeadQuery
     */
    public static function find(): LeadQuery
    {
        return new LeadQuery(static::class);
    }

    /**
     * @param bool $linkMode
     * @return string
     */
    public function getCommunicationInfo(bool $details = false, bool $linkMode = true): string
    {
        $str = [];
        $linkAttributes = ['target' => '_blank', 'data-pjax' => '0'];

        $countCalls = $countCallsOut = $countCallsIn = 0;
        $countSms = $countSmsIn = $countSmsOut = 0;
        $countEmail = $countEmailIn = $countEmailOut = 0;

        if ($details) {
            $countCallsOut = $this->getCountCalls(Call::CALL_TYPE_OUT);
            $countCallsIn = $this->getCountCalls(Call::CALL_TYPE_IN);

            $countSmsIn = $this->getCountSms(Sms::TYPE_INBOX);
            $countSmsOut = $this->getCountSms(Sms::TYPE_OUTBOX);

            $countEmailIn = $this->getCountEmails(EmailType::INBOX);
            $countEmailOut = $this->getCountEmails(EmailType::OUTBOX);
        } else {
            $countCalls = $this->getCountCalls();
            $countSms = $this->getCountSms();
            $countEmail = $this->getCountEmails();
        }

        $countClientChat = $this->getCountClientChat();

        if ($linkMode) {
            if ($countCalls || $countCallsOut || $countCallsIn) {
                if ($details) {
                    $callsText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-phone success']) .
                        ' ' . $countCallsOut . '/' .
                        $countCallsIn, ['title' => 'Calls Out / In']);
                } else {
                    $callsText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-phone success']) .
                        ' ' . $countCalls, ['title' => 'Calls']);
                }

                if (Auth::can('/call/index')) {
                    $str[] = Html::a(
                        $callsText,
                        Url::to(['/call-log/index', 'CallLogSearch[lead_id]' => $this->id]),
                        $linkAttributes
                    );
                } else {
                    $str[] = $callsText;
                }
            }

            if ($countSms || $countSmsIn || $countSmsOut) {
                if ($details) {
                    $smsText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-comments info']) .
                        ' ' . $countSmsOut . '/' .
                        $countSmsIn, ['title' => 'SMS Out / In']);
                } else {
                    $smsText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-comments info']) .
                        ' ' . $countSms, ['title' => 'SMS']);
                }

                if (Auth::can('/sms/index')) {
                    $str[] = Html::a(
                        $smsText,
                        Url::to(['/sms/index', 'SmsSearch[s_lead_id]' => $this->id]),
                        $linkAttributes
                    );
                } else {
                    $str[] = $smsText;
                }
            }

            if ($countEmail || $countEmailIn || $countEmailOut) {
                if ($details) {
                    $emilText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-envelope danger']) .
                        ' ' . $countEmailOut . '/' .
                        $countEmailIn, ['title' => 'Email Out / In']);
                } else {
                    $emilText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-envelope danger']) .
                        ' ' . $countEmail, ['title' => 'Email']);
                }

                if (Auth::can('/email/index')) {
                    $str[] = Html::a(
                        $emilText,
                        Url::to(['/email/index', 'EmailSearch[e_lead_id]' => $this->id]),
                        $linkAttributes
                    );
                } else {
                    $str[] = $emilText;
                }
            }


            if ($countClientChat) {
                $chatText = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-weixin warning']) .
                    ' ' . $countClientChat, ['title' => 'Client Chat']);
                if (Auth::can('/client-chat-crud/index')) {
                    $str[] = Html::a(
                        $chatText,
                        Url::to(['/client-chat-crud/index', 'ClientChatQaSearch[leadId]' => $this->id]),
                        $linkAttributes
                    );
                } else {
                    $str[] = $chatText;
                }
            }
        } else {
            if ($details) {
                if ($countCallsOut || $countCallsIn) {
                    $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-phone success']) .
                        ' ' . $countCallsOut . '/' .
                        $countCallsIn, ['title' => 'Calls Out / In']);
                }

                if ($countSmsOut || $countSmsIn) {
                    $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-comments info']) .
                        ' ' . $countSmsOut . '/' .
                        $countSmsIn, ['title' => 'SMS Out / In']);
                }

                if ($countEmailOut || $countEmailIn) {
                    $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-envelope danger']) .
                        ' ' . $countEmailOut . '/' .
                        $countEmailIn, ['title' => 'Email Out / In']);
                }
            } else {
                if ($countCalls) {
                    $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-phone success']) .
                        ' ' . $countCalls, ['title' => 'Calls']);
                }

                if ($countSms) {
                    $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-comments info']) .
                        ' ' . $countSms, ['title' => 'SMS']);
                }

                if ($countEmail) {
                    $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-envelope danger']) .
                        ' ' . $countEmail, ['title' => 'Email']);
                }
            }

            if ($countClientChat) {
                $str[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-weixin warning']) .
                    ' ' . $countClientChat, ['title' => 'Client Chat']);
            }
        }

        return implode(' | ', $str);
    }

    /**
     * @return bool
     */
    public function isInTrash(): bool
    {
        return $this->status === self::STATUS_TRASH;
    }

    public function getProjectId(): ?int
    {
        return $this->project_id;
    }

    public function getDepartmentId(): ?int
    {
        return $this->l_dep_id;
    }

    public function isMultiDestination(): bool
    {
        return $this->trip_type === self::TRIP_TYPE_MULTI_DESTINATION;
    }

    public function isRoundTrip(): bool
    {
        return $this->trip_type === self::TRIP_TYPE_ROUND_TRIP;
    }

    public function isOneWayTrip(): bool
    {
        return $this->trip_type === self::TRIP_TYPE_ONE_WAY;
    }

    public function isReadyForGa(): bool
    {
        return (GaHelper::getTrackingIdByLead($this) && GaHelper::getClientIdByLead($this));
    }

    public function isExistQuotesForSend(): bool
    {
        return Quote::find()
            ->andWhere(['lead_id' => $this->id])
            ->andWhere(['status' => [Quote::STATUS_CREATED, Quote::STATUS_SENT, Quote::STATUS_OPENED]])
            ->exists();
    }

    public function getTypeCreateName(): string
    {
        return self::TYPE_CREATE_LIST[$this->l_type_create] ?? 'Undefined';
    }

    public function isBusiness(): bool
    {
        return in_array($this->project_id, SettingHelper::getBusinessProjectIds(), true)
            || $this->cabin === self::CABIN_BUSINESS
            || $this->cabin === self::CABIN_FIRST;
    }

    /**
     * @return string
     */
    public function getFlightDetailsPaxFormatted(): string
    {
        $content = [];

        $fdData = $this->getFlightDetails();


        $paxData = [];
        $paxStr = '';

        if ($this->adults) {
            $paxData[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-male']) .
                ' ' . $this->adults, ['title' => 'adult']);
        }

        if ($this->children) {
            $paxData[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-child']) .
                ' ' . $this->children, ['title' => 'child']);
        }

        if ($this->infants) {
            $paxData[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-info']) .
                ' ' . $this->infants, ['title' => 'infant']);
        }

        if ($fdData) {
            $content[] = $fdData;
        }

        if ($paxData) {
            $paxStr = implode(' / ', $paxData);
        }

        $content[] = ($paxStr ? $paxStr . ', ' : '') . Html::tag(
            'span',
            $this->getCabinClassName(),
            ['title' => 'Cabin']
        );

        return !empty($content) ? implode('<br/>', $content) : '-';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getClientFormatted(): string
    {
        $client = $this->client;
        if ($client) {
            $clientName = $client->getFullName(); //first_name . ' ' . $client->last_name;
            if ($clientName === 'ClientName') {
                $clientName = '-';
            } else {
                $clientName = Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($clientName);
            }

            if ($client->isExcluded()) {
                $clientName = ClientFormatter::formatExclude($client)  . $clientName;
            }

            $str = '';
            //$str = $lead->client && $lead->client->clientEmails ? '<i class="fa fa-envelope"></i> ' .
            // implode(' <br><i class="fa fa-envelope"></i> ',
            // \yii\helpers\ArrayHelper::map($lead->client->clientEmails, 'email', 'email')) . '' : '';
            //$str .= $lead->client && $lead->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' .
            // implode(' <br><i class="fa fa-phone"></i> ',
            // \yii\helpers\ArrayHelper::map($lead->client->clientPhones, 'phone', 'phone')) . '' : '';

            $clientName .= /*'<br>' .*/ $str;
        } else {
            $clientName = '-';
        }

        return $clientName . '<br/>' . ClientTimeFormatter::format($this->getClientTime2(), $this->offset_gmt);
    }

    /**
     * @return string
     */
    public function getQuoteInfoFormatted(): string
    {
        $quotes = $this->getQuoteSendInfo();
        //return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>',
        // ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
//        return '<span title="Send:' . ((int) $quotes['send_q']) .
//            ' / Total:' . ($quotes['send_q'] + $quotes['not_send_q']) . '">' .
//            ($quotes['send_q'] ?: '-') . ' / ' . ($quotes['send_q'] + $quotes['not_send_q']) . '</span>';

        if (empty($quotes['send_q']) && empty($quotes['not_send_q'])) {
            return '-';
        }

        return Html::tag(
            'span',
            ($quotes['send_q'] ?: '-') . ' / ' . ($quotes['send_q'] + $quotes['not_send_q']),
            ['title' => 'Send:' . ((int) $quotes['send_q']) .
            ' / Total:' . ($quotes['send_q'] + $quotes['not_send_q'])]
        );
    }

    public function extraQueue(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        if ($this->isExtraQueue()) {
            return;
        }

        $this->recordEvent(
            new LeadExtraQueueEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->changeOwner($newOwnerId);

        $this->setStatus(self::STATUS_EXTRA_QUEUE);
    }

    public function toBusinessExtraQueue(?int $newOwnerId = null, ?int $creatorId = null, ?string $reason = ''): void
    {
        if ($this->isBusinessExtraQueue()) {
            return;
        }
        $this->changeOwner($newOwnerId);

        $this->recordEvent(
            new LeadBusinessExtraQueueEvent(
                $this,
                $this->status,
                $this->employee_id,
                $newOwnerId,
                $creatorId,
                $reason
            )
        );

        $this->setStatus(self::STATUS_BUSINESS_EXTRA_QUEUE);
    }

    public function hasTakenFromExtraToProcessing(): bool
    {
        if ($this->isProcessing()) {
            $lastLeadFlow = $this->lastLeadFlow;
            if (!$lastLeadFlow) {
                return false;
            }
            if ($lastLeadFlow->lf_from_status_id === self::STATUS_EXTRA_QUEUE) {
                return true;
            }
        }
        return false;
    }
    public function hasTakenFromBonusToProcessing(): bool
    {
        if ($this->isProcessing()) {
            $lastLeadFlow = $this->lastLeadFlow;
            if (!$lastLeadFlow) {
                return false;
            }
            if ($lastLeadFlow->lf_from_status_id === self::STATUS_FOLLOW_UP) {
                return true;
            }
        }
        return false;
    }

    public function close(?string $leadStatusReasonKey = null, ?int $creatorId = null, ?string $reasonComment = ''): void
    {
        if ($this->isClosed()) {
            return;
        }
        $this->recordEvent(new LeadCloseEvent($this, $leadStatusReasonKey, $this->status, $creatorId, $reasonComment));
        $this->setStatus(self::STATUS_CLOSED);
    }

    public function isExtraQueue(): bool
    {
        return $this->status === self::STATUS_EXTRA_QUEUE;
    }

    public function isBusinessExtraQueue(): bool
    {
        return $this->status === self::STATUS_BUSINESS_EXTRA_QUEUE;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function getDeepLink(array $settings): string
    {
        $url = '/smart/search/';

        $flightSegments = $this->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all();
        $segmentsStr = [];
        $signedParams = [];
        $flexParams = ['departureFlexd' => null];
        foreach ($flightSegments as $key => $entry) {
            if ($this->isRoundTrip()) {
                $trip = '';
                if ($key == 0) {
                    $trip = $entry['origin'] . '-' . $entry['destination'] . '/';
                    $flexParams['departureFlexd'] = LeadUrlHelper::formatFlexOptions($entry['flexibility'], $entry['flexibility_type']);
                }
                if ($key == 1) {
                    if (
                        $flightSegments[0]['origin'] !== $flightSegments[1]['destination'] ||
                        ($flightSegments[0]['origin'] == $flightSegments[1]['destination'] && $flightSegments[0]['destination'] !== $flightSegments[1]['origin'])
                    ) {
                        $trip = $entry['origin'] . '-' . $entry['destination'] . '/';
                    }
                    $flexParams['returnFlexd'] = LeadUrlHelper::formatFlexOptions($entry['flexibility'], $entry['flexibility_type']);
                }
                $segmentsStr[] = $trip . date('Y-m-d', strtotime($entry['departure']));
            } else {
                $segmentsStr[] = $entry['origin'] . '-' . $entry['destination'] . '/' . date('Y-m-d', strtotime($entry['departure']));
                if ($this->isOneWayTrip()) {
                    $flexParams['departureFlexd'] = LeadUrlHelper::formatFlexOptions($entry['flexibility'], $entry['flexibility_type']);
                }
                if ($this->isMultiDestination()) {
                    $flexParams['departureFlexd'] .= LeadUrlHelper::formatFlexOptions($entry['flexibility'], $entry['flexibility_type']);
                    if ($key > 1) {
                        //unset($flexParams['departureFlexd']);
                        $flexParams = ['departureFlexd' => null];
                    }
                }
            }
            array_push($signedParams, $entry['origin'], $entry['destination']);
        }

        $segments =  implode('/', $segmentsStr);
        $signedParams[] = strval($this->id);
        $params = [
            'tt' => $this->trip_type,
            'cabin' => strtolower($this->cabin),
            'adt' => $this->adults,
            'chd' => $this->children,
            'inf' => $this->infants,
            'leadId' => $this->id,
            'a' => $this->employee_id,
            //TODO: refactor, use singleton, get from container doesn't work
            //'redirectUrl' => urlencode(base64_encode($settings['redirectUrl']))
        ] + $flexParams;

        if ($this->leadPreferences && $this->leadPreferences->pref_currency != 'USD') {
            $params['currency'] = $this->leadPreferences->pref_currency;
        }

        if ($this->project && $cid = $this->project->getAirSearchCid()) {
            $params['cid'] = $cid;
            $signedParams[] = $cid;
        }

        $params[UrlSignature::FORM_QUERY_KEY] = UrlSignature::CalculateWithKey($signedParams, 'secretKey');

        return $url . $segments . '?' . http_build_query($params);
    }

    public function hasFlightDetails(): bool
    {
        return $this->leadFlightSegmentsCount > 0;
    }

    public function setCabinClassEconomy(): void
    {
        $this->cabin = self::CABIN_ECONOMY;
    }

    public function statusIsBusinessExtraQueue(): bool
    {
        return $this->status === self::STATUS_BUSINESS_EXTRA_QUEUE;
    }

    public function isBusinessType(): bool
    {
        return (bool) $this
            ->getLeadData()
            ->where(['ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT])
            ->andWhere(['ld_field_value' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS])
            ->count();
    }

    private function getNumberObjectTasksOrderByStatus(): array
    {
        if ($this->objectTaskOrderByStatus === null) {
            $this->objectTaskOrderByStatus = ObjectTask::countByStatus(
                ObjectTaskService::OBJECT_LEAD,
                $this->id
            );
        }

        return $this->objectTaskOrderByStatus;
    }

    public function hasObjectTasks(): bool
    {
        return !empty($this->getNumberObjectTasksOrderByStatus());
    }

    public function hasObjectTasksWithPendingStatus(): bool
    {
        return array_key_exists(ObjectTask::STATUS_PENDING, $this->getNumberObjectTasksOrderByStatus());
    }

    public function countObjectTaskWithPendingStatus(): int
    {
        return (int) ($this->getNumberObjectTasksOrderByStatus()[ObjectTask::STATUS_PENDING] ?? 0);
    }

    public function countObjectTask(): int
    {
        return array_sum(
            $this->getNumberObjectTasksOrderByStatus()
        );
    }
}
