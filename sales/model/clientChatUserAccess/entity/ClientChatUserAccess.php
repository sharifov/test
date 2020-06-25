<?php

namespace sales\model\clientChatUserAccess\entity;

use common\models\Employee;
use sales\dispatchers\NativeEventDispatcher;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserAccess\event\ClientChatUserAccessEvent;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_user_access".
 *
 * @property int $ccua_cch_id
 * @property int $ccua_user_id
 * @property int|null $ccua_status_id
 * @property string|null $ccua_created_dt
 * @property string|null $ccua_updated_dt
 *
 * @property ClientChat $ccuaCch
 * @property Employee $ccuaUser
 */
class ClientChatUserAccess extends \yii\db\ActiveRecord
{
	public const STATUS_PENDING = 1;
	public const STATUS_ACCEPT = 2;
	public const STATUS_BUSY = 3;
	public const STATUS_SKIP = 4;

	public const STATUS_LIST = [
		self::STATUS_PENDING => 'Pending',
		self::STATUS_ACCEPT => 'Accept',
		self::STATUS_BUSY => 'Busy',
		self::STATUS_SKIP => 'Skip'
	];

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ccua_created_dt', 'ccua_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ccua_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			]
		];
	}

    public function rules(): array
    {
        return [
            [['ccua_cch_id', 'ccua_user_id'], 'unique', 'targetAttribute' => ['ccua_cch_id', 'ccua_user_id']],

            ['ccua_cch_id', 'required'],
            ['ccua_cch_id', 'integer'],
            ['ccua_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccua_cch_id' => 'cch_id']],

            ['ccua_created_dt', 'safe'],

            ['ccua_status_id', 'integer'],

            ['ccua_updated_dt', 'safe'],

            ['ccua_user_id', 'required'],
            ['ccua_user_id', 'integer'],
            ['ccua_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccua_user_id' => 'id']],
        ];
    }

    public function getCcuaCch(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccua_cch_id']);
    }

    public function getCcuaUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccua_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccua_cch_id' => 'Cch ID',
            'ccua_user_id' => 'User',
            'ccua_status_id' => 'Status',
            'ccua_created_dt' => 'Created Dt',
            'ccua_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_user_access';
    }

    public static function create(int $channelId, int $userId): self
	{
		$access = new self();
		$access->ccua_cch_id = $channelId;
		$access->ccua_user_id = $userId;

		return $access;
	}

	public function pending(): void
	{
		$this->ccua_status_id = self::STATUS_PENDING;
	}

	/**
	 * @param int $userId
	 * @return array|ActiveRecord[]
	 */
	public static function pendingRequests(int $userId): array
	{
		return self::find()->byUserId($userId)->pending()->orderBy(['ccua_created_dt' => SORT_DESC])->all();
	}

	public static function statusExist(int $status): bool
	{
		return array_key_exists($status, self::STATUS_LIST);
	}

	public function setStatus(int $status): void
	{
		$this->ccua_status_id = $status;
	}

	public function isAccept(): bool
	{
		return $this->ccua_status_id === self::STATUS_ACCEPT;
	}

	public function isPending(): bool
	{
		return $this->ccua_status_id === self::STATUS_PENDING;
	}

	public function isSkip(): bool
	{
		return $this->ccua_status_id === self::STATUS_SKIP;
	}
}
