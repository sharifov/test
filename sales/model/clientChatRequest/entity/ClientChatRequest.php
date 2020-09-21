<?php

namespace sales\model\clientChatRequest\entity;

use sales\dispatchers\NativeEventDispatcher;
use sales\forms\clientChat\RealTimeStartChatForm;
use sales\model\clientChatRequest\event\ClientChatRequestEvents;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

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
		self::EVENT_CREATE_BY_AGENT => 'CREATE_BY_AGENT'
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

	public function getDepartmentFromData(): ?string
	{
		return $this->decodedData['visitor']['department'] ?? '';
	}

	public function getEmailFromData(): ?string
	{
		return $this->decodedData['visitor']['email'] ?? null;
	}

	public function getPhoneFromData(): ?string
	{
		return $this->decodedData['visitor']['phone'] ?? null;
	}

	public function getNameFromData():?string
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

	public function getPageUrl(): string
	{
		return $this->decodedData['page']['url'] ?? '';
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
     * @param int $eventId
     * @return array|ActiveRecord|null
     */
    public static function getLastRequestByVisitorId(string $visitorId, int $eventId)
    {
        return self::find()
            ->where(['ccr_visitor_id' => $visitorId])
            ->andWhere(['ccr_event' => $eventId])
            ->orderBy(['ccr_id' => SORT_DESC])
            ->one();
    }
}
