<?php

namespace sales\model\clientChatRequest\entity;

use sales\dispatchers\NativeEventDispatcher;
use sales\model\clientChatRequest\event\ClientChatRequestEvents;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
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
 * @property array|null $decodedData
 */
class ClientChatRequest extends \yii\db\ActiveRecord
{
	private const EVENT_GUEST_CONNECTED = 1;
    private const EVENT_ROOM_CONNECTED = 2;
    private const EVENT_ROOM_DISCONNECTED = 3;
    private const EVENT_GUEST_UTTERED = 4;
    private const EVENT_AGENT_UTTERED = 5;
    private const EVENT_DEPARTMENT_TRANSFER = 6;
    private const EVENT_AGENT_LEFT_ROOM = 7;
    private const EVENT_AGENT_JOINED_ROOM = 8;
    private const EVENT_USER_DEPARTMENT_TRANSFER = 9;

	private const EVENT_LIST = [
		self::EVENT_GUEST_CONNECTED => 'GUEST_CONNECTED',
		self::EVENT_ROOM_CONNECTED => 'ROOM_CONNECTED',
		self::EVENT_ROOM_DISCONNECTED => 'ROOM_DISCONNECTED',
		self::EVENT_GUEST_UTTERED => 'GUEST_UTTERED',
		self::EVENT_AGENT_UTTERED => 'AGENT_UTTERED',
		self::EVENT_DEPARTMENT_TRANSFER => 'DEPARTMENT_TRANSFER',
		self::EVENT_AGENT_LEFT_ROOM => 'AGENT_LEFT_ROOM',
		self::EVENT_AGENT_JOINED_ROOM => 'AGENT_JOINED_ROOM',
		self::EVENT_USER_DEPARTMENT_TRANSFER => 'USER_DEPARTMENT_TRANSFER'
	];

    public function rules(): array
    {
        return [
            ['ccr_created_dt', 'safe'],

            ['ccr_event', 'integer'],
            ['ccr_event', 'in', 'range' => self::getEventIdList()],

            ['ccr_json_data', 'string'],
            ['ccr_rid', 'string'],
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
            'ccr_rid' => 'Request ID',
            'ccr_json_data' => 'Json Data',
            'ccr_created_dt' => 'Created Dt',
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
		return json_decode($this->ccr_json_data, true);
	}

    public static function createByApi(ClientChatRequestApiForm $form): self
	{
		$_self = new self();
		$_self->ccr_event = $form->eventId;
		$_self->ccr_json_data = json_encode($form->data, JSON_THROW_ON_ERROR);
		$_self->ccr_rid = $form->rid;

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

	public function isGuestUttered(): bool
	{
		return self::EVENT_GUEST_UTTERED === $this->ccr_event;
	}

	public function isAgentUttered(): bool
	{
		return self::EVENT_AGENT_UTTERED === $this->ccr_event;
	}

	public function isDepartmentTransfer(): bool
	{
		return self::EVENT_DEPARTMENT_TRANSFER === $this->ccr_event;
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

	public function getProjectNameFromData(): string
	{
		return $this->decodedData['project'] ?? '';
	}

	public function getDepartmentIdFromData(): ?int
	{
		return (int)($this->decodedData['department'] ?? null);
	}

	public function getEmailFromData(): ?string
	{
		return $this->decodedData['email'] ?? null;
	}

	public function getNameFromData():string
	{
		return $this->decodedData['name'] ?? 'ClientName';
	}

	public function getVisitorOrUserIdFromData(): string
	{
		$data = $this->decodedData;
		if (isset($data['visitor'])) {
			return $data['visitor']['_id'] ?? '';
		}

		if (isset($data['user'])) {
			return $data['user']['_id'] ?? '';
		}

		return '';
	}
}
