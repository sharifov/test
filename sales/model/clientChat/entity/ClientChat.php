<?php

namespace sales\model\clientChat\entity;

use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\Project;
use sales\entities\cases\Cases;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
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
 * @property int|null $cch_case_id
 * @property int|null $cch_lead_id
 * @property string|null $cch_note
 * @property int|null $cch_status_id
 * @property string|null $cch_ip
 * @property int|null $cch_ua
 * @property int|null $cch_language_id
 * @property string|null $cch_created_dt
 * @property string|null $cch_updated_dt
 * @property int|null $cch_created_user_id
 * @property int|null $cch_updated_user_id
 *
 * @property Cases $cchCase
 * @property ClientChatRequest $cchCcr
 * @property Client $cchClient
 * @property ClientChatChannel $cchChannel
 * @property Department $cchDep
 * @property Lead $cchLead
 * @property Employee $cchOwnerUser
 * @property Project $cchProject
 */
class ClientChat extends \yii\db\ActiveRecord
{
	private const STATUS_GENERATED = 1;
	private const STATUS_CLOSED = 9;
	private const STATUS_PENDING = 2;

	private const STATUS_LIST = [
		self::STATUS_GENERATED => 'Generated',
		self::STATUS_PENDING => 'Pending',
		self::STATUS_CLOSED => 'Closed'
	];

	private const STATUS_CLASS_LIST = [
		self::STATUS_GENERATED => 'info',
		self::STATUS_PENDING => 'warning',
		self::STATUS_CLOSED => 'warning'
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
			'user' => [
				'class' => BlameableBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['cch_created_user_id', 'cch_updated_user_id'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['cch_updated_user_id'],
				],
			],
		];
	}

    public function rules(): array
    {
        return [
            ['cch_case_id', 'integer'],
            ['cch_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['cch_case_id' => 'cs_id']],

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

            ['cch_lead_id', 'integer'],
            ['cch_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['cch_lead_id' => 'id']],

            ['cch_note', 'string', 'max' => 255],

            ['cch_owner_user_id', 'integer'],
            ['cch_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cch_owner_user_id' => 'id']],

            ['cch_project_id', 'integer'],
            ['cch_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cch_project_id' => 'id']],

            ['cch_rid', 'string', 'max' => 150],

            ['cch_status_id', 'integer'],

            ['cch_title', 'string', 'max' => 50],

            ['cch_ua', 'integer'],

            ['cch_updated_dt', 'safe'],

            ['cch_updated_user_id', 'integer'],
			['cch_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cch_updated_user_id' => 'id']],
		];
    }

    public function getCchCase(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'cch_case_id']);
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

	public function getCchLanguage(): \yii\db\ActiveQuery
	{
		return $this->hasOne(Language::class, ['language_id' => 'cch_language_id']);
	}

    public function getCchLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'cch_lead_id']);
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

	public static function getStatusList(): array
	{
		return self::STATUS_LIST;
	}

	public function getStatusName(): ?string
	{
		return $this->cch_status_id ? self::getStatusList()[$this->cch_status_id] : null;
	}

	public function generated(): void
	{
		$this->cch_status_id = self::STATUS_GENERATED;
	}

	public static function getStatusClassList(): array
	{
		return self::STATUS_CLASS_LIST;
	}

	public function getStatusClass()
	{
		return self::getStatusClassList()[$this->cch_status_id] ?? 'secondary';
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
            'cch_case_id' => 'Case ID',
            'cch_lead_id' => 'Lead ID',
            'cch_note' => 'Note',
            'cch_status_id' => 'Status ID',
            'cch_ip' => 'IP',
            'cch_ua' => 'User Access',
            'cch_language_id' => 'Language ID',
            'cch_created_dt' => 'Created Dt',
            'cch_updated_dt' => 'Updated Dt',
            'cch_created_user_id' => 'Created User',
            'cch_updated_user_id' => 'Updated User',
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
}
