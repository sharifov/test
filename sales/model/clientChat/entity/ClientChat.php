<?php

namespace sales\model\clientChat\entity;

use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\Project;
use sales\entities\cases\Cases;
use sales\entities\EventTrait;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\event\ClientChatManageStatusLogEvent;
use sales\model\clientChat\event\ClientChatSetStatusCloseEvent;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat".
 *
 * @property int $cch_id
 * @property string|null $cch_rid
 * @property int|null $cch_ccr_id
 * @property string|null $cch_title
 * @property string|null $cch_description
 * @property int|null $cch_project_id
 * @property int|null $cch_dep_id
 * @property int|null $cch_channel_id
 * @property int|null $cch_client_id
 * @property int|null $cch_owner_user_id
 * @property string|null $cch_note
 * @property int|null $cch_status_id
 * @property string|null $cch_ip
 * @property int|null $cch_ua
 * @property int|null $cch_language_id
 * @property string|null $cch_created_dt
 * @property string|null $cch_updated_dt
 * @property int|null $cch_created_user_id
 * @property int|null $cch_updated_user_id
 * @property int|null $cch_client_online
 * @property int|null $cch_source_type_id
 * @property int|null $cch_missed
 * @property int|null $cch_parent_id
 *
 * @property ClientChatRequest $cchCcr
 * @property Client $cchClient
 * @property ClientChatChannel $cchChannel
 * @property Department $cchDep
 * @property Employee $cchOwnerUser
 * @property Project $cchProject
 * @property ClientChatNote[] $notes
 * @property ClientChatVisitor $ccv
 * @property Lead[] $leads
 * @property Cases[] $cases
 * @property ClientChatFeedback $feedback
 * @property ClientChatLastMessage $lastMessage
 */
class ClientChat extends \yii\db\ActiveRecord
{
	use EventTrait;

	public const STATUS_NEW = 1;
	public const STATUS_CLOSED = 9;
	public const STATUS_PENDING = 2;
	public const STATUS_IN_PROGRESS = 4;
	public const STATUS_TRANSFER = 3;

	public const MISSED = 1;

	private const STATUS_LIST = [
		self::STATUS_NEW => 'New',
		self::STATUS_PENDING => 'Pending',
		self::STATUS_CLOSED => 'Closed',
		self::STATUS_TRANSFER => 'Transfer',
		self::STATUS_IN_PROGRESS => 'In Progress',
	];

	private const STATUS_CLASS_LIST = [
		self::STATUS_NEW => 'info',
		self::STATUS_PENDING => 'warning',
		self::STATUS_CLOSED => 'danger',
		self::STATUS_TRANSFER => 'warning',
		self::STATUS_IN_PROGRESS => 'info',
	];

	public const TAB_ALL = 0;
	public const TAB_ACTIVE = 1;
	public const TAB_ARCHIVE = 2;

	public const TAB_LIST_NAME = [
		self::TAB_ACTIVE => 'Active',
		self::TAB_ARCHIVE => 'Closed'
	];

	public const SOURCE_TYPE_CLIENT = 1;
	public const SOURCE_TYPE_AGENT = 2;
	public const SOURCE_TYPE_TRANSFER = 3;

	private const SOURCE_TYPE_LIST = [
		self::SOURCE_TYPE_CLIENT => 'Client',
		self::SOURCE_TYPE_AGENT => 'Agent',
		self::SOURCE_TYPE_TRANSFER => 'Transfer',
	];

    public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['cch_created_dt', 'cch_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['cch_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
		];
	}

    public function rules(): array
    {
        return [
            ['cch_ccr_id', 'integer'],
            ['cch_ccr_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatRequest::class, 'targetAttribute' => ['cch_ccr_id' => 'ccr_id']],

            ['cch_channel_id', 'integer'],
			['cch_channel_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatChannel::class, 'targetAttribute' => ['cch_channel_id' => 'ccc_id']],

            ['cch_client_id', 'integer'],
            ['cch_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cch_client_id' => 'id']],

            ['cch_created_dt', 'safe'],

            ['cch_created_user_id', 'integer'],
			['cch_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cch_created_user_id' => 'id']],

			['cch_dep_id', 'integer'],
            ['cch_dep_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['cch_dep_id' => 'dep_id']],

            ['cch_description', 'string', 'max' => 255],

            ['cch_ip', 'string', 'max' => 20],

			['cch_language_id', 'string', 'max' => 5],
			['cch_language_id', 'default', 'value' => null],
			['cch_language_id', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['cch_language_id' => 'language_id']],

            ['cch_note', 'string', 'max' => 255],

            ['cch_owner_user_id', 'integer'],
            ['cch_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cch_owner_user_id' => 'id']],

            ['cch_project_id', 'integer'],
            ['cch_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cch_project_id' => 'id']],

            ['cch_rid', 'string', 'max' => 150],

            ['cch_status_id', 'integer'],
            ['cch_source_type_id', 'integer'],
            ['cch_missed', 'integer'],
            ['cch_parent_id', 'integer'],

            ['cch_title', 'string', 'max' => 50],

            ['cch_ua', 'integer'],
            ['cch_client_online', 'integer'],

            ['cch_updated_dt', 'safe'],

            ['cch_updated_user_id', 'integer'],
			['cch_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cch_updated_user_id' => 'id']],
		];
    }

    public function getCchCcr(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatRequest::class, ['ccr_id' => 'cch_ccr_id']);
    }

	public function getCchChannel(): \yii\db\ActiveQuery
	{
		return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'cch_channel_id']);
	}

    public function getCchClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cch_client_id']);
    }

    public function getCchDep(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'cch_dep_id']);
    }

	public function getLanguage(): \yii\db\ActiveQuery
	{
		return $this->hasOne(Language::class, ['language_id' => 'cch_language_id']);
	}

    public function getCchOwnerUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cch_owner_user_id']);
    }

    public function getCchProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'cch_project_id']);
    }

	public function getCchCreatedUser(): \yii\db\ActiveQuery
	{
		return $this->hasOne(Employee::class, ['id' => 'cch_created_user_id']);
	}

	public function getCchUpdatedUser(): \yii\db\ActiveQuery
	{
		return $this->hasOne(Employee::class, ['id' => 'cch_updated_user_id']);
	}

	public function getNotes(): ActiveQuery
	{
		return $this->hasMany(ClientChatNote::class, ['ccn_chat_id' => 'cch_id']);
	}

	public function getCcv(): ActiveQuery
	{
		return $this->hasOne(ClientChatVisitor::class, ['ccv_client_id' => 'cch_client_id', 'ccv_cch_id' => 'cch_id']);
	}

	public function getLeads(): ActiveQuery
	{
		return $this->hasMany(Lead::class, ['id' => 'ccl_lead_id'])->viaTable(ClientChatLead::tableName(), ['ccl_chat_id' => 'cch_id']);
	}

	public function getCases(): ActiveQuery
	{
		return $this->hasMany(Cases::class, ['cs_id' => 'cccs_case_id'])->viaTable(ClientChatCase::tableName(), ['cccs_chat_id' => 'cch_id']);
	}

	public function getFeedback(): ActiveQuery
	{
		return $this->hasOne(ClientChatFeedback::class, ['ccf_client_chat_id' => 'cch_id']);
	}

	public function getLastMessage(): ActiveQuery
	{
		return $this->hasOne(ClientChatLastMessage::class, ['cclm_cch_id' => 'cch_id']);
	}

	public static function getStatusList(): array
	{
		return self::STATUS_LIST;
	}

	public static function getSourceTypeList(): array
	{
		return self::SOURCE_TYPE_LIST;
	}

	public function getStatusName(): ?string
	{
		return $this->cch_status_id ? self::getStatusList()[$this->cch_status_id] : null;
	}

	public function getSourceTypeName(): ?string
	{
		return $this->cch_source_type_id ? self::getSourceTypeList()[$this->cch_source_type_id] : null;
	}

	public function pending(?int $userId, int $action, ?string $description = null): void
	{
		$this->recordEvent(new ClientChatManageStatusLogEvent($this, $this->cch_status_id, self::STATUS_PENDING, $this->cch_owner_user_id, $userId, $description, $this->cch_channel_id, $action));
		$this->cch_status_id = self::STATUS_PENDING;
	}

	public function close(int $userId, int $action, ?string $description = null): void
	{
		$this->recordEvent(new ClientChatManageStatusLogEvent($this, $this->cch_status_id, self::STATUS_CLOSED, $this->cch_owner_user_id, $userId, $description, $this->cch_channel_id, $action));
		$this->recordEvent(new ClientChatSetStatusCloseEvent($this->cch_id));
		$this->cch_status_id = self::STATUS_CLOSED;
	}

	public function transfer(int $userId, int $action, ?string $description = null): void
	{
		$this->recordEvent(new ClientChatManageStatusLogEvent($this, $this->cch_status_id, self::STATUS_TRANSFER, $this->cch_owner_user_id, $userId, $description, $this->cch_channel_id, $action));
		$this->cch_status_id = self::STATUS_TRANSFER;
	}

	public function inProgress(?int $userId, $action): void
	{
		$this->recordEvent(new ClientChatManageStatusLogEvent($this, $this->cch_status_id, self::STATUS_IN_PROGRESS, $this->cch_owner_user_id, $userId, null, $this->cch_channel_id, $action));
		$this->cch_status_id = self::STATUS_IN_PROGRESS;
	}

	public function isTransfer(): bool
	{
		return $this->cch_status_id === self::STATUS_TRANSFER;
	}

	public function isClosed(): bool
	{
		return $this->cch_status_id === self::STATUS_CLOSED;
	}

	public static function getStatusClassList(): array
	{
		return self::STATUS_CLASS_LIST;
	}

	public function getStatusClass()
	{
		return self::getStatusClassList()[$this->cch_status_id] ?? '';
	}

	public function getClientStatusMessage(): string
	{
		return ClientChatHelper::getClientName($this) . ClientChatHelper::getClientStatusMessage($this);
	}

	public function assignOwner(?int $userId): self
	{
		if (!$this->isTransfer() && !is_null($userId) && $this->cchOwnerUser && $this->cch_owner_user_id !== $userId) {
			throw new \DomainException('Client Chat already assigned to: ' . $this->cchOwnerUser->username, ClientChatCodeException::CC_OWNER_ALREADY_ASSIGNED);
		}
		$this->cch_owner_user_id = $userId;
		return $this;
	}

	public function removeOwner(): void
	{
		$this->cch_owner_user_id = null;
	}

    public function attributeLabels(): array
    {
        return [
            'cch_id' => 'ID',
            'cch_rid' => 'Room ID',
            'cch_ccr_id' => 'Request ID',
            'cch_title' => 'Title',
            'cch_description' => 'Description',
            'cch_project_id' => 'Project',
            'cch_dep_id' => 'Department',
            'cch_channel_id' => 'Channel',
            'cch_client_id' => 'Client',
            'cch_owner_user_id' => 'Owner User',
            'cch_note' => 'Note',
            'cch_status_id' => 'Status ID',
            'cch_ip' => 'IP',
            'cch_ua' => 'User Access',
            'cch_language_id' => 'Language ID',
            'cch_created_dt' => 'Created Dt',
            'cch_updated_dt' => 'Updated Dt',
            'cch_created_user_id' => 'Created User',
            'cch_updated_user_id' => 'Updated User',
            'cch_client_online' => 'Client Online',
            'cch_source_type_id' => 'Source Type',
            'cch_missed' => 'Missed',
            'cch_parent_id' => 'Parent Chat',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat';
    }

    public static function getTabList(): array
	{
		return self::TAB_LIST_NAME;
	}

	public static function isTabActive(int $tab): bool
	{
		return $tab === self::TAB_ACTIVE;
	}

	public static function isTabClosed(int $tab): bool
	{
		return $tab === self::TAB_ARCHIVE;
	}

	public static function isTabAll(int $tab): bool
	{
		return $tab === self::TAB_ALL;
	}

    public function isAssignedLead(int $leadId): bool
    {
        foreach ($this->leads as $lead) {
            if ($lead->id === $leadId) {
                return true;
            }
        }
        return false;
	}

	public function isOwner(int $userId): bool
	{
		return $this->cch_owner_user_id === $userId;
	}

	public static function clone(ClientChatCloneDto $dto): ClientChat
	{
		$chat = new ClientChat();
		$chat->cch_rid = $dto->cchRid;
		$chat->cch_ccr_id = $dto->cchCcrId;
		$chat->cch_project_id = $dto->cchProjectId;
		$chat->cch_client_id = $dto->cchClientId;
		$chat->cch_owner_user_id = $dto->ownerId;
		$chat->cch_client_online = $dto->isOnline;
		return $chat;
	}
}
