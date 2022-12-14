<?php

namespace src\model\clientChatUserAccess\entity;

use common\models\Employee;
use src\dispatchers\NativeEventDispatcher;
use src\entities\EventTrait;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatUserAccess\event\ClientChatUserAccessEvent;
use src\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetEvent;
use src\model\clientChatUserAccess\useCase\manageRequest\UserAccessAcceptPending;
use src\model\clientChatUserAccess\useCase\manageRequest\UserAccessAcceptTransfer;
use src\model\clientChatUserAccess\useCase\manageRequest\UserAccessManageRequestInterface;
use src\model\clientChatUserAccess\useCase\manageRequest\UserAccessSkipPending;
use src\model\clientChatUserAccess\useCase\manageRequest\UserAccessSkipTransfer;
use src\model\clientChatUserAccess\useCase\manageRequest\UserAccessTakeIdle;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_user_access".
 *
 * @property int $ccua_id
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
    use EventTrait;

    public const STATUS_PENDING = 1;
    public const STATUS_ACCEPT = 2;
    public const STATUS_BUSY = 3;
    public const STATUS_SKIP = 4;
    public const STATUS_TRANSFER_ACCEPT = 5;
    public const STATUS_TRANSFER_SKIP = 7;
    public const STATUS_CANCELED = 6;
    public const STATUS_TAKE = 8;

    public const STATUS_LIST = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPT => 'Accept',
        self::STATUS_BUSY => 'Busy',
        self::STATUS_SKIP => 'Skip',
        self::STATUS_TRANSFER_ACCEPT => 'Transfer Accept',
        self::STATUS_TRANSFER_SKIP => 'Transfer Skip',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_TAKE => 'Take',
    ];

    public const STATUS_CLASS_LIST = [
        self::STATUS_PENDING => 'info',
        self::STATUS_ACCEPT => 'success',
        self::STATUS_BUSY => 'warning',
        self::STATUS_SKIP => 'danger',
        self::STATUS_TRANSFER_ACCEPT => 'warning',
        self::STATUS_TRANSFER_SKIP => 'danger',
        self::STATUS_CANCELED => 'danger',
        self::STATUS_TAKE => 'info',
    ];

    public const STATUS_ACCEPT_GROUP = [
        self::STATUS_ACCEPT,
        self::STATUS_TAKE,
    ];

    public const STATUS_SKIP_GROUP = [
        self::STATUS_SKIP,
        self::STATUS_TRANSFER_SKIP,
        self::STATUS_CANCELED,
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
            ['ccua_cch_id', 'required'],
            ['ccua_cch_id', 'integer'],
            ['ccua_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccua_cch_id' => 'cch_id']],

            ['ccua_created_dt', 'safe'],

            ['ccua_status_id', 'integer'],

            ['ccua_updated_dt', 'safe'],

            ['ccua_user_id', 'required'],
            ['ccua_user_id', 'integer'],
            ['ccua_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccua_user_id' => 'id']],
            ['ccua_status_id', 'checkIfAlreadyAccepted']
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
            'ccua_id' => 'Ccua ID',
            'ccua_cch_id' => 'Chat ID',
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

    public static function create(int $chatId, int $userId): self
    {
        $access = new self();
        $access->ccua_cch_id = $chatId;
        $access->ccua_user_id = $userId;

        return $access;
    }

    public function pending(): void
    {
        $this->ccua_status_id = self::STATUS_PENDING;
    }

    public function accept(): void
    {
        $this->ccua_status_id = self::STATUS_ACCEPT;
    }

    public function take(): void
    {
        $this->ccua_status_id = self::STATUS_TAKE;
    }

    public function transferAccept(): void
    {
        $this->ccua_status_id = self::STATUS_TRANSFER_ACCEPT;
    }

    public function canceled(): void
    {
        $this->ccua_status_id = self::STATUS_CANCELED;
    }

    /**
     * @param int $userId
     * @return array|ActiveRecord[]
     */
    public static function pendingRequests(int $userId): array
    {
        return self::find()->byUserId($userId)->pending()->innerJoin(ClientChat::tableName(), 'ccua_cch_id = cch_id')->orderBy(['cch_status_id' => SORT_DESC, 'ccua_created_dt' => SORT_ASC, 'cch_created_dt' => SORT_ASC,])->all();
    }

    public static function statusExist(int $status): bool
    {
        return array_key_exists($status, self::STATUS_LIST);
    }

    public static function getAccessManageRequest(int $statusId, ClientChat $chat, Employee $owner, ClientChatUserAccess $access): UserAccessManageRequestInterface
    {
        if ($statusId === self::STATUS_TRANSFER_ACCEPT) {
            return new UserAccessAcceptTransfer($chat, $access, $statusId);
        }

        if ($statusId === self::STATUS_TRANSFER_SKIP) {
            return new UserAccessSkipTransfer($chat, $access, $statusId);
        }

        if ($statusId === self::STATUS_ACCEPT) {
            return new UserAccessAcceptPending($chat, $access, $statusId);
        }

        if ($statusId === self::STATUS_SKIP) {
            return new UserAccessSkipPending($chat, $access, $statusId);
        }

        if ($statusId === self::STATUS_TAKE) {
            return new UserAccessTakeIdle($chat, $access, $statusId, $owner);
        }
        throw new \DomainException('Unknown access action');
    }

    public function setStatus(int $status): void
    {
        $this->ccua_status_id = $status;
    }

    public function isTransferAccept(): bool
    {
        return $this->ccua_status_id === self::STATUS_TRANSFER_ACCEPT;
    }

    public function isTransferSkip(): bool
    {
        return $this->ccua_status_id === self::STATUS_TRANSFER_SKIP;
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

    public function checkIfAlreadyAccepted($attributes): void
    {
        $clientChat = $this->ccuaCch;
        if ($clientChat && !$clientChat->isTransfer() && $this->isAccept() && self::find()->byClientChat($this->ccua_cch_id)->accepted()->exists()) {
            $this->addError('ccua_status_id', 'Chat request already accepted');
        }

        if ($clientChat->isTransfer() && $this->isPending() && self::find()->byClientChat($this->ccua_cch_id)->byUserId($this->ccua_user_id)->pending()->exists()) {
            $user = $this->ccuaUser;
            $this->addError('ccua_user_id', 'User: ' . $user->username . ' has already received a request with this chat');
        }
    }

    public function getTimeByChatStatus(): string
    {
        if ($this->ccuaCch && $this->ccuaCch->isTransfer()) {
            return (string)$this->ccua_created_dt;
        }
        return (string)$this->ccuaCch->cch_created_dt;
    }

    public static function isInStatusAcceptGroupList(int $statusId): bool
    {
        return in_array($statusId, self::STATUS_ACCEPT_GROUP);
    }

    public static function isInStatusSkipGroupList(int $statusId): bool
    {
        return in_array($statusId, self::STATUS_SKIP_GROUP);
    }
}
