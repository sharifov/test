<?php

namespace src\model\clientChatRequest\entity;

use DateTime;
use src\forms\clientChat\RealTimeStartChatForm;
use src\model\clientChat\ClientChatPlatform;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\AgentUtteredEventCreator;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\ChatRequestEventCreator;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\GuestConnectedEventCreator;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\GuestDisconnectedEventCreator;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\GuestUtteredEventCreator;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\RoomConnectedEventCreator;
use src\model\clientChatRequest\useCase\api\create\requestEventCreator\TrackEventCreator;
use yii\base\InvalidParamException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_request".
 *
 * @property int $ccr_id
 * @property int|null $ccr_event
 * @property string|null $ccr_rid
 * @property string|null $ccr_json_data
 * @property string|null $ccr_created_dt
 * @property string|null $ccr_visitor_id
 * @property array|null $decodedData
 * @property int|null $ccr_job_id
 */
class ClientChatRequest extends \yii\db\ActiveRecord
{
    public const EVENT_GUEST_CONNECTED = 1;
    public const EVENT_GUEST_DISCONNECTED = 11;
    public const EVENT_ROOM_CONNECTED = 2;
    public const EVENT_ROOM_DISCONNECTED = 3;
    public const EVENT_GUEST_UTTERED = 4;
    public const EVENT_AGENT_UTTERED = 5;
    public const EVENT_DEPARTMENT_TRANSFER = 6;
    public const EVENT_AGENT_LEFT_ROOM = 7;
    public const EVENT_AGENT_JOINED_ROOM = 8;
    public const EVENT_USER_DEPARTMENT_TRANSFER = 9;
    public const EVENT_TRACK = 10;
    public const EVENT_CREATE_BY_AGENT = 12;
    public const EVENT_LEAVE_FEEDBACK = 13;
    public const EVENT_FEEDBACK_REQUESTED = 14;
    public const EVENT_FEEDBACK_SUBMITTED = 15;
    public const EVENT_FEEDBACK_REJECTED = 16;
    public const EVENT_FORM_SUBMITTED = 17;

    private const EVENT_LIST = [
        self::EVENT_GUEST_CONNECTED => 'GUEST_CONNECTED',
        self::EVENT_GUEST_DISCONNECTED => 'GUEST_DISCONNECTED',
        self::EVENT_ROOM_CONNECTED => 'ROOM_CONNECTED',
        self::EVENT_ROOM_DISCONNECTED => 'ROOM_DISCONNECTED',
        self::EVENT_GUEST_UTTERED => 'GUEST_UTTERED',
        self::EVENT_AGENT_UTTERED => 'AGENT_UTTERED',
        self::EVENT_DEPARTMENT_TRANSFER => 'DEPARTMENT_TRANSFER',
        self::EVENT_AGENT_LEFT_ROOM => 'AGENT_LEFT_ROOM',
        self::EVENT_AGENT_JOINED_ROOM => 'AGENT_JOINED_ROOM',
        self::EVENT_USER_DEPARTMENT_TRANSFER => 'USER_DEPARTMENT_TRANSFER',
        self::EVENT_TRACK => 'TRACK_EVENT',
        self::EVENT_LEAVE_FEEDBACK => 'LEAVE_FEEDBACK',
        self::EVENT_FEEDBACK_REQUESTED => 'FEEDBACK_REQUESTED',
        self::EVENT_FEEDBACK_SUBMITTED => 'FEEDBACK_SUBMITTED',
        self::EVENT_FEEDBACK_REJECTED => 'FEEDBACK_REJECTED',
        self::EVENT_CREATE_BY_AGENT => 'CREATE_BY_AGENT',
        self::EVENT_FORM_SUBMITTED => 'FORM_SUBMITTED',
    ];

    private const EVENT_CREATORS = [
        self::EVENT_ROOM_CONNECTED => RoomConnectedEventCreator::class,
        self::EVENT_GUEST_DISCONNECTED => GuestDisconnectedEventCreator::class,
        self::EVENT_TRACK => TrackEventCreator::class,
        self::EVENT_GUEST_UTTERED => GuestUtteredEventCreator::class,
        self::EVENT_AGENT_UTTERED => AgentUtteredEventCreator::class,
        self::EVENT_GUEST_CONNECTED => GuestConnectedEventCreator::class
    ];

    private array $decodedJsonData = [];

    public function rules(): array
    {
        return [
            ['ccr_created_dt', 'safe'],

            ['ccr_event', 'integer'],
            ['ccr_event', 'in', 'range' => self::getEventIdList()],

            ['ccr_json_data', 'string'],
            ['ccr_rid', 'string'],
            [['ccr_visitor_id'], 'string', 'max' => 100],

            [['ccr_job_id'], 'integer'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ccr_id' => 'ID',
            'ccr_event' => 'Event',
            'ccr_rid' => 'Room ID',
            'ccr_json_data' => 'Json Data',
            'ccr_created_dt' => 'Created',
            'ccr_visitor_id' => 'Visitor ID',
            'ccr_job_id' => 'Job ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_request';
    }

    public function getDecodedData(): array
    {
        if (!$this->decodedJsonData) {
            return $this->decodedJsonData = json_decode($this->ccr_json_data, true);
        }
        return $this->decodedJsonData;
    }

    public static function createByApi(ClientChatRequestApiForm $form): self
    {
        $_self = new self();
        $_self->ccr_event = $form->eventId;
        $_self->ccr_json_data = json_encode($form->data, JSON_THROW_ON_ERROR);
        $_self->ccr_rid = $form->rid;
        $_self->ccr_visitor_id = $form->data['visitor']['id'] ?? null;

        return $_self;
    }

    public static function createByAgent(RealTimeStartChatForm $form): self
    {
        $_self = new self();
        $_self->ccr_event = self::EVENT_CREATE_BY_AGENT;
        $_self->ccr_json_data = $form->dataToJson();
        $_self->ccr_rid = $form->rid;
        $_self->ccr_visitor_id = $form->visitorId;
        return $_self;
    }

    public function isGuestConnected(): bool
    {
        return self::EVENT_GUEST_CONNECTED === $this->ccr_event;
    }

    public function isRoomConnected(): bool
    {
        return self::EVENT_ROOM_CONNECTED === $this->ccr_event;
    }

    public function isGuestDisconnected(): bool
    {
        return self::EVENT_GUEST_DISCONNECTED === $this->ccr_event;
    }

    public function isGuestUttered(): bool
    {
        return self::EVENT_GUEST_UTTERED === $this->ccr_event;
    }

    public static function isMessage(?int $eventId): bool
    {
        return self::EVENT_AGENT_UTTERED === $eventId || self::EVENT_GUEST_UTTERED === $eventId;
    }

    public function isAgentUttered(): bool
    {
        return self::EVENT_AGENT_UTTERED === $this->ccr_event;
    }

    public function isDepartmentTransfer(): bool
    {
        return self::EVENT_DEPARTMENT_TRANSFER === $this->ccr_event;
    }

    public function isTrackEvent(): bool
    {
        return self::EVENT_TRACK === $this->ccr_event;
    }

    public static function getEventList(): array
    {
        return self::EVENT_LIST;
    }

    public static function getEventIdList(): array
    {
        return array_keys(self::getEventList());
    }

    public static function getEventIdByName(string $name): ?int
    {
        return array_keys(self::getEventList(), $name)[0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getEventName(): ?string
    {
        return self::EVENT_LIST[$this->ccr_event] ?? '-';
    }

    public function getProjectKeyFromData(): string
    {
        return $this->decodedData['visitor']['project'] ?? '';
    }

    public function getChannelIdFromData(): int
    {
        return (int)($this->decodedData['visitor']['channel'] ?? null);
    }

    public function getEmailFromData(): ?string
    {
        return $this->decodedData['visitor']['email'] ?? null;
    }

    public function getPhoneFromData(): ?string
    {
        return $this->decodedData['visitor']['phone'] ?? null;
    }

    public function getNameFromData(): ?string
    {
        return $this->decodedData['visitor']['name'] ?? null;
    }

    public function getUserIdFromData(): string
    {
        return $this->decodedData['visitor']['user_id'] ?? '';
    }

    public function getClientRcId(): string
    {
        return $this->decodedData['visitor']['id'] ?? '';
    }

    public function getClientUuId(): string
    {
        return $this->decodedData['visitor']['uuid'] ?? '';
    }

    public function getVisitorId(): string
    {
        return $this->decodedData['visitorId'] ?? '';
    }

    public function getLeadIds()
    {
        return $this->decodedData['visitor']['leadIds'] ?? [];
    }

    public function getCaseIds()
    {
        return $this->decodedData['visitor']['caseIds'] ?? [];
    }

    public function getPageUrl(): string
    {
        return $this->decodedData['page']['url'] ?? '';
    }

    public function getSourceCid(): string
    {
        return $this->decodedData['sources']['cid'] ?? '';
    }

    public function getPlatformId(): int
    {
        return ClientChatPlatform::getPlatformIdByName($this->decodedData['visitor']['platform'] ?? '');
    }

    public function getFlightSearchParameters(): array
    {
        if (isset($this->decodedData['parameters']) && !is_array($this->decodedData['parameters'])) {
            throw new \InvalidArgumentException('Property "parameters" in ClientChatRequest.json_data is not array');
        }
        return $this->decodedData['parameters'] ?? [];
    }

    /**
     * @param string $rid
     * @return array|ActiveRecord|null
     */
    public static function getLastRequestByRid(string $rid)
    {
        return self::find()
            ->where(['ccr_rid' => $rid])
            ->orderBy(['ccr_id' => SORT_DESC])
            ->one();
    }

    /**
     * @param string $visitorId
     * @param string $jsonData
     * @return array|ActiveRecord|null
     */
    public static function getLastRequestPageByVisitorId(string $visitorId, string $jsonData = 'url":"http')
    {
        return self::find()
            ->where(['ccr_visitor_id' => $visitorId])
            ->andWhere(['like', 'ccr_json_data', $jsonData])
            ->orderBy(['ccr_id' => SORT_DESC])
            ->one();
    }

    /**
     * @return object
     */
    public static function getDb()
    {
        return \Yii::$app->get('db_postgres');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ["ccr_id"];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Calculate from and to dates from a given date.
     * Given date -> from = start of the month, to = next month start date
     *
     * @param DateTime $date partition start date
     * @return array DateTime table_name created table
     * @throws \RuntimeException any errors occurred during execution
     */
    public static function partitionDatesFrom(DateTime $date): array
    {
        $monthBegin = date('Y-m-d', strtotime(date_format($date, 'Y-m-1')));
        if (!$monthBegin) {
            throw new \RuntimeException("invalid partition start date");
        }

        $partitionStartDate = date_create_from_format('Y-m-d', $monthBegin);
        $partitionEndDate = date_create_from_format('Y-m-d', $monthBegin);

        date_add($partitionEndDate, date_interval_create_from_date_string("1 month"));

        return [$partitionStartDate, $partitionEndDate];
    }

    /**
     * Create a partition table with indicated from and to date
     *
     * @param DateTime $partFromDateTime partition start date
     * @param DateTime $partToDateTime partition end date
     * @return string table_name created table
     * @throws \yii\db\Exception
     */
    public static function createMonthlyPartition(DateTime $partFromDateTime, DateTime $partToDateTime): string
    {
        $db = self::getDb();
        $partTableName = self::tableName() . "_" . date_format($partFromDateTime, "Y_m");
        $cmd = $db->createCommand("create table " . $partTableName . " PARTITION OF " . self::tableName() .
            " FOR VALUES FROM ('" . date_format($partFromDateTime, "Y-m-d") . "') TO ('" . date_format($partToDateTime, "Y-m-d") . "')");
        $cmd->execute();
        return $partTableName;
    }

    public static function getEventCreatorByEventId(int $id): ?ChatRequestEventCreator
    {
        if (isset(self::EVENT_CREATORS[$id])) {
            $creator = self::EVENT_CREATORS[$id];
            return \Yii::createObject($creator);
        }
        return null;
    }

    public static function getPrevModels($prevId, $limit, $filters = null): array
    {
        if (isset($filters)) {
            $mainQuery = static::find()
                ->where(['>', 'ccr_id', $prevId])
                ->andFilterWhere($filters)
                ->orderBy(['ccr_id' => SORT_ASC])
                ->limit($limit + 1);
            return static::find()
                ->from(['C' => $mainQuery])
                ->orderBy(['ccr_id' => SORT_DESC])
                ->all();
        }

        $mainQuery = static::find()
            ->where(['>', 'ccr_id', $prevId])
            ->orderBy(['ccr_id' => SORT_ASC])
            ->limit($limit + 1);
        return static::find()
            ->from(['C' => $mainQuery])
            ->orderBy(['ccr_id' => SORT_DESC])
            ->limit($limit + 1)
            ->all();
    }
}
